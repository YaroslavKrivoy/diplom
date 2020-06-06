<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel\Product;

use Aheadworks\Autorelated\Model\Config;
use Magento\Sales\Api\Data\OrderItemInterface;
use Aheadworks\Autorelated\Model\Source;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory;

/**
 * Class Collection
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Product
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Collection ID field
     */
    const ID_FIELD = 'entity_id';

    /**
     * Flag for stock filter applied
     */
    const STOCK_FLAG = 'has_stock_status_filter';

    /**
     * Name of the table with WBTAB data
     */
    const WBTAB_TABLE_NAME = 'aw_autorelated_wbtab_product';

    /**
     * Name of the table with WVTAV data
     */
    const WVTAV_TABLE_NAME = 'aw_autorelated_wvtav_index';

    /**
     * Name of the select part for staging preview flag
     */
    const DISABLE_STAGING_PREVIEW_PART_KEY = 'disable_staging_preview';

    /**
     * Value of staging preview flag for ARP collection
     */
    const DISABLE_STAGING_PREVIEW_FLAG_VALUE = false;

    /**
     * @var StatusFactory
     */
    private $stockStatusFactory;

    /**
     * @var Status
     */
    private $stockStatusResource;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory $stockStatusFactory
     * @param Config $config
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\StatusFactory $stockStatusFactory,
        Config $config
    ) {
        $this->stockStatusFactory = $stockStatusFactory;
        $this->config = $config;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addAttributeToSelect('price');
        $this->updateStagingFlagForSelect();
        return $this;
    }

    /**
     * Update if necessary EE staging flag
     */
    private function updateStagingFlagForSelect()
    {
        if ($this->isPartExists(self::DISABLE_STAGING_PREVIEW_PART_KEY)) {
            $this->getSelect()->setPart(
                self::DISABLE_STAGING_PREVIEW_PART_KEY,
                self::DISABLE_STAGING_PREVIEW_FLAG_VALUE
            );
        }
    }

    /**
     * Adding sort type to collection
     *
     * @param int $sortType
     * @return $this
     */
    public function addProductSorting($sortType)
    {
        switch ($sortType) {
            case Source\Sort::SORT_BY_BESTSELLER:
                $days = $this->config->getNumberDaysBuildBestsellersList();
                $this
                    ->getSelect()
                    ->joinLeft(
                        new \Zend_Db_Expr(
                            '(SELECT SUM(' . OrderItemInterface::QTY_ORDERED . ') as qty_ordered, product_id'
                            . ' FROM ' . $this->getTable('sales_order_item')
                            . ' where (DATE(' . OrderItemInterface::CREATED_AT . ') >= CURRENT_DATE - INTERVAL '
                                . $days . ' DAY)'
                            . ' GROUP BY product_id)'
                        ),
                        'e.entity_id = t.product_id',
                        []
                    )
                    ->group('e.entity_id')
                ;
                $this->getSelect()->order('SUM(t.qty_ordered) DESC');
                break;
            case Source\Sort::SORT_BY_NEWEST:
                $this->addAttributeToSort(
                    'news_from_date',
                    'desc'
                )->addAttributeToSort(
                    'created_at',
                    'desc'
                );
                break;
            case Source\Sort::SORT_BY_PRICE_ASC:
                $this->addOrder('price', self::SORT_ORDER_ASC);
                break;
            case Source\Sort::SORT_BY_PRICE_DESC:
                $this->addOrder('price');
                break;
            case Source\Sort::SORT_BY_RANDOM:
                $allIds = $this->getAllIds();
                $allIds = array_unique($allIds);
                shuffle($allIds);
                $this->getSelect()
                    ->order(new \Zend_DB_Expr('FIELD(e.entity_id, ' . implode(',', $allIds) . ') '));
                break;
        }
        return $this;
    }

    /**
     * Adding only in stock products filter to product collection
     *
     * @return $this
     */
    public function addInStockFilter()
    {
        $isFilterInStock = true;
        if (!$this->getFlag(self::STOCK_FLAG)) {
            $resource = $this->getStockStatusResource();
            $resource->addStockDataToCollection(
                $this,
                $isFilterInStock
            );
            $this->setFlag(self::STOCK_FLAG, true);
        }
        return $this;
    }

    /**
     * @return Status
     */
    protected function getStockStatusResource()
    {
        if (empty($this->stockStatusResource)) {
            $this->stockStatusResource = $this->stockStatusFactory->create();
        }
        return $this->stockStatusResource;
    }

    /**
     * Overwrite parent getAllIds method. Delete resetJoinLeft
     *
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idField = self::ID_FIELD;
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns("e.{$idField}");
        $idsSelect->limit($limit, $offset);
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Reset collection
     *
     * @return $this
     */
    public function resetCollection()
    {
        $this->_reset();
        $this->setFlag(self::STOCK_FLAG, false);
        return $this;
    }

    /**
     * Is part exist
     *
     * @param string $partKey
     * @return bool
     */
    protected function isPartExists($partKey)
    {
        try {
            $this->getSelect()->getPart($partKey);
        } catch (\Zend_Db_Select_Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Join table with WBTAB data
     *
     * @return $this
     */
    public function joinWbtabProducts()
    {
        $idField = self::ID_FIELD;
        $this->getSelect()->joinInner(
            ['wbtab' => $this->getTable(self::WBTAB_TABLE_NAME)],
            'wbtab.related_product_id = e.' . $idField,
            []
        );
        return $this;
    }

    /**
     * Add filtering WBTAB data for product with specified id
     *
     * @param int $productId
     * @return $this
     */
    public function addWbtabProductFiltering($productId)
    {
        $this->getSelect()->where('wbtab.product_id = ?', $productId);
        return $this;
    }

    /**
     * Add products sorting by WBTAB rating
     *
     * @return $this
     */
    public function addWbtabProductSorting()
    {
        $select = $this->getSelect();
        $select->order('wbtab.orders_count DESC');
        return $this;
    }

    /**
     * Join table with WVTAV data
     *
     * @return $this
     */
    public function joinWvtavProducts()
    {
        $idField = self::ID_FIELD;
        $this->getSelect()->joinInner(
            ['wvtav' => $this->getTable(self::WVTAV_TABLE_NAME)],
            'wvtav.slave_product_id = e.' . $idField,
            []
        );
        return $this;
    }

    /**
     * Add filtering WVTAV data for product with specified id
     *
     * @param int $productId
     * @return $this
     */
    public function addWvtavProductFiltering($productId)
    {
        $this->getSelect()->where('wvtav.master_product_id = ?', $productId);
        return $this;
    }

    /**
     * Add products sorting by WVTAV rating
     *
     * @return $this
     */
    public function addWvtavProductSorting()
    {
        $select = $this->getSelect();
        $select->order('wvtav.rating DESC');
        return $this;
    }

    /**
     * Filter product collection by category of product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return $this
     */
    public function addWvtavOnlyOneCategoryFilter(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $categories = $product
            ->getCategoryCollection()
            ->load();
        if ($categories->count()) {
            $categoryIds = [];
            foreach ($categories as $category) {
                if (!$category->getChildrenCount()) {
                    $categoryIds[] = $category->getId();
                }
            }
            $this->addCategoriesFilter(['eq' => $categoryIds]);
        }

        return $this;
    }

    /**
     * Filter product collection by higher price
     *
     * @param double $price
     * @return $this
     */
    public function addWvtavOnlyPriceHigherFilter($price)
    {
        $this->addFinalPrice();
        $this->getSelect()->where('price_index.final_price > ?', $price);
        return $this;
    }

    /**
     * Join select for native related products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $relatedProductCollection
     * @return $this
     */
    public function joinNativeRelatedProducts($relatedProductCollection)
    {
        $relatedProductCollectionSelect = '(' . (string)$relatedProductCollection->getSelectSql() . ')';
        $this->getSelect()->joinInner(
            ['native_related_select' => new \Zend_Db_Expr($relatedProductCollectionSelect)],
            'e.entity_id = native_related_select.entity_id',
            []
        );
        return $this;
    }

    /**
     * Add products sorting by native related products position
     *
     * @return $this
     */
    public function addNativeRelatedProductsSorting()
    {
        $this->getSelect()->order('native_related_select.position ASC');
        return $this;
    }
}
