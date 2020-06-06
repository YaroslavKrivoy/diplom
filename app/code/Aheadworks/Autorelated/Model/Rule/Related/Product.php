<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Related;

use Aheadworks\Autorelated\Model\Rule\Related\Condition\CombineFactory;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as ActionCollectionFactory;
use Aheadworks\Autorelated\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Aheadworks\Autorelated\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Iterator as ResourceIterator;

/**
 * Class Product
 *
 * @package Aheadworks\Autorelated\Model\Rule\Related
 */
class Product extends AbstractModel
{
    /**
     * Store matched product Ids
     *
     * @var array
     */
    private $productIds;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ResourceIterator
     */
    private $resourceIterator;

    /**
     * @var CombineFactory
     */
    private $combineFactory;

    /**
     * @var ActionCollectionFactory
     */
    private $actionCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $combineFactory
     * @param ActionCollectionFactory $actionCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     * @param ResourceIterator $resourceIterator
     * @param ProductCollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        ActionCollectionFactory $actionCollectionFactory,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        ResourceIterator $resourceIterator,
        ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->combineFactory = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->resourceIterator = $resourceIterator;
        $this->productFactory = $productFactory;
        parent::__construct($context, $registry, $formFactory, $localeDate, null, null, $data);
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Aheadworks\Autorelated\Model\Rule\Related\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * Getter for rule actions collection
     *
     * @return \Magento\CatalogRule\Model\Rule\Action\Collection
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    /**
     * Reset rule combine conditions
     *
     * @param \Aheadworks\Autorelated\Model\Rule\Related\Condition\Combine|null $conditions
     * @return $this
     */
    public function _resetConditions($conditions = null)
    {
        parent::_resetConditions($conditions);
        $this->getConditions($conditions)
            ->setId('related_conditions')
            ->setPrefix('related');
        return $this;
    }

    /**
     * Get validated product ids
     *
     * @param int $productId
     * @param array $additionalParams
     * @return array
     */
    public function getMatchingProductIds($productId, $additionalParams = [])
    {
        if ($this->productIds === null) {
            $this->productIds = [];
            $this->setCollectedAttributes([]);

            /** @var ProductCollection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $this->getConditions()->collectValidatedAttributes($productCollection, $productId, $additionalParams);
            $productIds = $productCollection->getAllIds();
            $this->productIds = array_unique($productIds);
        }

        return $this->productIds;
    }
}
