<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel\Rule;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use \Magento\Framework\Data\Collection\EntityFactoryInterface;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use \Magento\Framework\Event\ManagerInterface;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Autorelated\Model\Rule;
use Aheadworks\Autorelated\Model\ResourceModel\Rule as RuleResource;
use Aheadworks\Autorelated\Model\Config;
use Aheadworks\Autorelated\Model\Rule\BlockTitle\StoreResolver as BlockTitleStoreResolver;

/**
 * Class Collection
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Rule
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RuleInterface::ID;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var BlockTitleStoreResolver
     */
    private $blockTitleStoreResolver;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Config $config
     * @param BlockTitleStoreResolver $blockTitleStoreResolver
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $config,
        BlockTitleStoreResolver $blockTitleStoreResolver,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->blockTitleStoreResolver = $blockTitleStoreResolver;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(Rule::class, RuleResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $profitQuery = $this->getConnection()->select()
            ->from(
                ['main_table' => $this->getTable('aw_autorelated_profit')],
                [
                    'view_count' => 'COALESCE(main_table.view_count, 0)',
                    'click_count' => 'COALESCE(main_table.click_count, 0)',
                    'ctr' => 'COALESCE('
                        . '(100/COALESCE(main_table.view_count, 0) * COALESCE(main_table.click_count, 0))'
                        . ', 0)'
                ]
            )->joinRight(
                ['rule' => $this->getTable('aw_autorelated_rule')],
                'main_table.rule_id = rule.id',
                [
                    'rule_id' => 'rule.id'
                ]
            );
        $this->getSelect()
            ->joinLeft(
                ['profit' => $profitQuery],
                'main_table.id = profit.rule_id',
                ['view_count', 'click_count', 'ctr']
            );

        $this->joinWvtavParamsTable();
        $this->addFilterToMap('id', 'main_table.id');

        return $this;
    }

    /**
     * Attach WVTAV params table data to collection items
     *
     * @return $this
     */
    protected function joinWvtavParamsTable()
    {
        $wvtavParamsTable = $this->getTable('aw_autorelated_rule_wvtav_params');
        $this
            ->getSelect()
            ->joinLeft(
                ['wvtavParamsTable' => $wvtavParamsTable],
                'wvtavParamsTable.rule_id = main_table.id',
                [
                    Rule::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_ONE_CATEGORY =>
                        'wvtavParamsTable.suggest_only_one_category',
                    Rule::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_PRICE_HIGHER =>
                        'wvtavParamsTable.suggest_only_price_higher',
                ]
            )
        ;
        return $this;
    }

    /**
     * Attach relation table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @return void
     */
    protected function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['store_linkage_table' => $this->getTable($tableName)])
                ->where('store_linkage_table.' . $linkageColumnName . ' IN (?)', $ids);
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $resultIds = [];
                $id = $item->getData($columnName);
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        if (is_array($columnNameRelationTable)) {
                            $fieldValue = [];
                            foreach ($columnNameRelationTable as $columnNameRelation) {
                                $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                            }
                            $resultIds[] = $fieldValue;
                        } else {
                            $resultIds[] = $data[$columnNameRelationTable];
                        }
                    }
                }
                $item->setData($fieldName, $resultIds);
            }
        }
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @return void
     */
    protected function joinLinkageTable($tableName, $columnName, $linkageColumnName, $columnFilter)
    {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $columnFilter.'_table';
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                "main_table." . $columnName . " = {$linkageTableName}." . $linkageColumnName,
                []
            )
            ->group('main_table.' . $columnName);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable('aw_autorelated_rule_store', 'id', 'rule_id', 'store_id', 'store_ids');
        $this->attachRelationTable(
            'aw_autorelated_rule_customer_group',
            'id',
            'rule_id',
            'customer_group_id',
            'customer_group_ids'
        );
        $this->attachRelationTable(
            'aw_autorelated_rule_block_title',
            'id',
            'rule_id',
            ['store_id', 'value'],
            'title_store_values'
        );
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            $this->attachRelationTable(
                'aw_autorelated_rule_customer_segment',
                'id',
                'rule_id',
                'customer_segment_id',
                'customer_segment_ids'
            );
        }

        /** @var \Magento\Framework\DataObject $item */
        foreach ($this as $item) {
            if ($storeValue = $this->blockTitleStoreResolver->getValueByStoreId(
                $item->getTitleStoreValues(),
                $this->storeId
            )) {
                $item->setData(RuleInterface::TITLE, $storeValue);
            }
        }

        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable('aw_autorelated_rule_store', 'id', 'rule_id', 'store_id');
        $this->joinLinkageTable('aw_autorelated_rule_customer_group', 'id', 'rule_id', 'customer_group_id');
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            $this->joinLinkageTable('aw_autorelated_rule_customer_segment', 'id', 'rule_id', 'customer_segment_id');
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Add store filter
     *
     * @param int|array $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if (!is_array($store)) {
            $store = [$store];
        }

        $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        $this->addFilter('store_id', ['in' => $store], 'public');

        return $this;
    }

    /**
     * Add customer group filter
     *
     * @param int|array $customerGroup
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroup)
    {
        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }

        $this->addFilter('customer_group_id', ['in' => $customerGroup], 'public');

        return $this;
    }

    /**
     * Add enterprise customer segment filter
     *
     * @param int|array $customerSegment
     * @return $this
     */
    public function addCustomerSegmentFilter($customerSegment)
    {
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            if (!is_array($customerSegment)) {
                $customerSegment = [$customerSegment];
            }

            $this->addFilter(
                'customer_segment_id',
                [
                    ['null' => true],
                    ['in' => $customerSegment]
                ],
                'public'
            );
        }

        return $this;
    }

    /**
     * Add rule id filter
     *
     * @param int|array $ruleId
     * @return $this
     */
    public function addRuleIdFilter($ruleId)
    {
        if (!is_array($ruleId)) {
            $ruleId = [$ruleId];
        }

        $this->addFilter('id', ['in' => $ruleId], 'public');

        return $this;
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }
}
