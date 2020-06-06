<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Related;

use Aheadworks\Autorelated\Model\Rule\Viewed\Condition\CombineFactory;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as ActionCollectionFactory;
use Aheadworks\Autorelated\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Model\ResourceModel\Iterator as ResourceIterator;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Aheadworks\Autorelated\Model\Rule\Viewed\Product as ViewedProduct;

/**
 * Class CategoryProduct
 *
 * @package Aheadworks\Autorelated\Model\Rule\Related
 */
class CategoryProduct extends ViewedProduct
{
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
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $combineFactory,
            $actionCollectionFactory,
            $storeManager,
            $productFactory,
            $resourceIterator,
            $productCollectionFactory,
            $data
        );
    }

    /**
     * Getter for rule conditions collection
     *
     * @return \Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Combine
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
     * @param \Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Combine|null $conditions
     * @return $this
     */
    protected function _resetConditions($conditions = null)
    {
        parent::_resetConditions($conditions);
        $this->getConditions($conditions)
            ->setId('category_related_conditions')
            ->setPrefix('category_related');
        return $this;
    }
}
