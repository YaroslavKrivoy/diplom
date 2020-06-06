<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Viewed\Condition;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\Autorelated\Model\Rule\Viewed\Condition
 */
class Validator
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if product matches provided rule conditions
     *
     * @param RuleInterface $rule
     * @param int $productId
     * @return bool
     */
    public function isProductValid($rule, $productId)
    {
        $result = true;
        $conditions = $rule->getViewedProductRule()->getConditions();
        if ($conditions && $productId) {
            try {
                $storeId = $this->storeManager->getStore()->getId();
                $product = $this->productRepository->getById($productId, false, $storeId);
                $result = $conditions->validate($product);
            } catch (\Exception $exception) {
                $result = false;
            }
        }

        return $result;
    }
}
