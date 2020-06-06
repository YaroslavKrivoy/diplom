<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Viewed;

use Aheadworks\Autorelated\Model\Rule\Viewed\Condition\CombineFactory;
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
 * @package Aheadworks\Autorelated\Model\Rule\Viewed
 */
class Product extends AbstractModel
{
    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $productIds = null;

    /**
     * @var CombineFactory
     */
    protected $combineFactory;

    /**
     * @var ActionCollectionFactory
     */
    protected $actionCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ResourceIterator
     */
    protected $resourceIterator;

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
            ->setId('viewed_conditions')
            ->setPrefix('viewed');
        return $this;
    }

    /**
     * Get validated product ids
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if ($this->productIds === null) {
            $this->productIds = [];
            $this->setCollectedAttributes([]);

            /** @var ProductCollection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'product' => $this->productFactory->create()
                ]
            );
        }
        return $this->productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        try {
            $website = $this->storeManager->getWebsite();
            if ($website->getDefaultStore() !== null) {
                $defaultStoreId = $website->getDefaultStore()->getId();
                $product->setStoreId($defaultStoreId);
                if ($this->getConditions()->validate($product)) {
                    $this->productIds[] = $product->getId();
                }
            }
        } catch (\Exception $exception) {
        }
    }
}
