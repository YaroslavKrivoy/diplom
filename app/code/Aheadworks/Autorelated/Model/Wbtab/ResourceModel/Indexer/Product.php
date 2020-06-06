<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wbtab\ResourceModel\Indexer;

use Magento\Indexer\Model\ResourceModel\AbstractResource;
use Magento\Sales\Model\Order;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Product
 * @package Aheadworks\Autorelated\Model\Wbtab\ResourceModel\Indexer
 */
class Product extends AbstractResource
{
    /**
     * @var int
     */
    const INSERT_PER_QUERY = 500;

    /**
     * @var string
     */
    const PRODUCT_TYPE_GROUPED = 'grouped';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $linkField;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy
     * @param MetadataPool $metadataPool
     * @param mixed $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->metadataPool = $metadataPool;
        $this->linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    /**
     * Define main product index table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_autorelated_wbtab_product', 'product_id');
    }

    /**
     * Reindex all product data
     *
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->clearTemporaryIndexTable();
        $this->beginTransaction();
        try {
            $toInsert = $this->getProductData();
            $this->insertProductDataToTable($toInsert);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        $this->syncData();
        return $this;
    }

    /**
     * Reindex product data for defined ids
     *
     * @param array|int $ids
     * @return $this
     * @throws \Exception
     */
    public function reindexRows($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        list($toUpdate, $ids) = $this->getProductData($ids);
        $this->beginTransaction();
        try {
            $this->getConnection()->delete(
                $this->getMainTable(),
                ['product_id IN (?)' => $ids]
            );
            $this->insertProductDataToTable($toUpdate, false);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Get data for insert to index table
     *
     * @param array|null $entityIds
     * @return array
     */
    private function getProductData($entityIds = null)
    {
        $connection = $this->getConnection();

        $selects = [
            $this->getProducts($connection, $entityIds),
            $this->getProductsRelatedWithGrouped($connection, $entityIds),
            $this->getRelatedGroupedProducts($connection, $entityIds)
        ];
        $unionSelect = new \Magento\Framework\DB\Sql\UnionExpression(
            $selects,
            \Magento\Framework\DB\Select::SQL_UNION_ALL
        );
        $tmpSelect = $connection->select()
            ->from(['tmp' => new \Zend_Db_Expr('(' . $unionSelect . ')')])
            ->group(['tmp.product_id', 'tmp.store_id', 'tmp.related_product_id']);

        $result = $connection->fetchAll($tmpSelect);
        if ($entityIds) {
            $ids = array_unique($connection->fetchCol($tmpSelect));
            return [$result, $ids];
        }

        return $result;
    }

    /**
     * Prepare data and partial insert to index or main table
     *
     * @param array $data
     * @param bool|true $intoIndexTable
     * @return $this
     */
    private function insertProductDataToTable($data, $intoIndexTable = true)
    {
        $counter = 0;
        $toInsert = [];
        foreach ($data as $row) {
            $counter++;
            $toInsert[] = $row;
            if ($counter % self::INSERT_PER_QUERY == 0) {
                $this->insertToTable($toInsert, $intoIndexTable);
                $toInsert = [];
            }
        }
        $this->insertToTable($toInsert, $intoIndexTable);
        return $this;
    }

    /**
     * Insert to index table
     *
     * @param array $toInsert
     * @param bool|true $intoIndexTable
     * @return $this
     */
    private function insertToTable($toInsert, $intoIndexTable = true)
    {
        $table = $intoIndexTable
            ? $this->getTable($this->getIdxTable())
            : $this->getMainTable();
        if (count($toInsert)) {
            $this->getConnection()->insertMultiple(
                $table,
                $toInsert
            );
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param array|null $entityIds
     * @return \Magento\Framework\DB\Select
     */
    private function getRelatedGroupedProducts($connection, $entityIds = null)
    {
        $select = $connection->select();
        $select->from(['soi' => $this->getTable('sales_order_item')], '')
            ->columns([
                'product_id' => 'soi.product_id',
                'store_id' => 'so.store_id',
                'orders_count' => 'count(*)'
            ])
            ->join(['so' => $this->getTable('sales_order')], 'so.entity_id = soi.order_id', '')
            ->join(
                ['related_soi' => $this->getTable('sales_order_item')],
                $connection->quoteInto(
                    'related_soi.order_id = soi.order_id AND soi.product_id != related_soi.product_id '
                    . 'AND related_soi.product_type = ?',
                    self::PRODUCT_TYPE_GROUPED
                ),
                ''
            )
            ->join(
                ['related_grouped' => $this->getTable('catalog_product_link')],
                'related_grouped.linked_product_id = related_soi.product_id'
                . ' AND link_type_id = ' . \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED,
                ''
            )
            ->join(['cpe' => $this->getTable('catalog_product_entity')], 'cpe.entity_id = soi.product_id', '')
            ->join(['cpe2' => $this->getTable('catalog_product_entity')], 'cpe2.entity_id = related_soi.product_id', '')
            ->join(
                ['cpe3' => $this->getTable('catalog_product_entity')],
                'cpe3.' . $this->linkField . ' = related_grouped.product_id',
                ['related_product_id' => 'cpe3.entity_id']
            )
            ->where('so.total_item_count > 1')
            ->where('so.state = ?', Order::STATE_COMPLETE)
            ->where('soi.parent_item_id IS null')
            ->where('soi.product_type != ?', self::PRODUCT_TYPE_GROUPED)
            ->where('related_soi.parent_item_id IS null')
            ->group(['soi.product_id', 'related_grouped.product_id', 'so.store_id']);
        if ($entityIds) {
            $select->where('soi.product_id IN (?)', $entityIds);
        }

        return $select;
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param array|null $entityIds
     * @return \Magento\Framework\DB\Select
     */
    private function getProductsRelatedWithGrouped($connection, $entityIds = null)
    {
        $select = $connection->select();
        $select->from(['soi' => $this->getTable('sales_order_item')], '')
            ->columns([
                'product_id' => 'cpe3.entity_id',
                'store_id' => 'so.store_id',
                'orders_count' => 'count(*)'
            ])
            ->join(['so' => $this->getTable('sales_order')], 'so.entity_id = soi.order_id', '')
            ->join(
                ['related_soi' => $this->getTable('sales_order_item')],
                $connection->quoteInto(
                    'related_soi.order_id = soi.order_id AND soi.product_id != related_soi.product_id '
                    . 'AND related_soi.product_type != ?',
                    self::PRODUCT_TYPE_GROUPED
                ),
                ''
            )
            ->join(
                ['related_grouped' => $this->getTable('catalog_product_link')],
                'related_grouped.linked_product_id  = soi.product_id'
                . ' AND link_type_id = ' . \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED,
                ['related_product_id' => 'related_soi.product_id']
            )
            ->join(['cpe' => $this->getTable('catalog_product_entity')], 'cpe.entity_id = soi.product_id', '')
            ->join(['cpe2' => $this->getTable('catalog_product_entity')], 'cpe2.entity_id = related_soi.product_id', '')
            ->join(
                ['cpe3' => $this->getTable('catalog_product_entity')],
                'cpe3.' . $this->linkField . ' = related_grouped.product_id',
                ''
            )
            ->where('so.total_item_count > 1')
            ->where('so.state = ?', Order::STATE_COMPLETE)
            ->where('soi.parent_item_id IS null')
            ->where('soi.product_type = ?', self::PRODUCT_TYPE_GROUPED)
            ->where('related_soi.parent_item_id IS null')
            ->group(['related_soi.product_id', 'cpe3.entity_id', 'so.store_id']);
        if ($entityIds) {
            $select->where('related_grouped.linked_product_id IN (?)', $entityIds);
        }

        return $select;
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param array|null $entityIds
     * @return \Magento\Framework\DB\Select
     */
    private function getProducts($connection, $entityIds = null)
    {
        $select = $connection->select();
        $select->from(['soi' => $this->getTable('sales_order_item')], '')
            ->columns([
                'product_id' => 'soi.product_id',
                'store_id' => 'so.store_id',
                'orders_count' => 'count(*)'
            ])
            ->join(['so' => $this->getTable('sales_order')], 'so.entity_id = soi.order_id', '')
            ->join(
                ['related_soi' => $this->getTable('sales_order_item')],
                'related_soi.order_id = soi.order_id AND soi.product_id != related_soi.product_id',
                ['related_product_id' => 'related_soi.product_id']
            )
            ->join(['cpe' => $this->getTable('catalog_product_entity')], 'cpe.entity_id = soi.product_id', '')
            ->join(['cpe2' => $this->getTable('catalog_product_entity')], 'cpe2.entity_id = related_soi.product_id', '')
            ->where('so.total_item_count > 1')
            ->where('so.state = ?', Order::STATE_COMPLETE)
            ->where('soi.parent_item_id IS null')
            ->where('related_soi.parent_item_id IS null')
            ->group(['soi.product_id', 'related_soi.product_id', 'so.store_id']);
        if ($entityIds) {
            $select->where('soi.product_id IN (?)', $entityIds);
        }

        return $select;
    }
}
