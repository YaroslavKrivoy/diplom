<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Related;

use Magento\Catalog\Model\Product\Visibility;
use Aheadworks\Autorelated\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Autorelated\Model\Rule\CurrentPageObject;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Autorelated\Model\Rule\TypeResolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Aheadworks\Autorelated\Model\Source\ProductConditionType;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\Autorelated\Model\Rule\Related
 */
class Validator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Visibility
     */
    private $productVisibility;

    /**
     * @var ProductCollection|null
     */
    private $productCollection;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CurrentPageObject
     */
    private $currentPageObject;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var TypeResolver
     */
    private $ruleTypeResolver;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param Visibility $productVisibility
     * @param ProductCollection $productCollection
     * @param CheckoutSession $checkoutSession
     * @param CurrentPageObject $currentPageObject
     * @param StoreManagerInterface $storeManager
     * @param ProductRepository $productRepository
     * @param TypeResolver $ruleTypeResolver
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Visibility $productVisibility,
        ProductCollection $productCollection,
        CheckoutSession $checkoutSession,
        CurrentPageObject $currentPageObject,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        TypeResolver $ruleTypeResolver,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productVisibility = $productVisibility;
        $this->productCollection = $productCollection;
        $this->checkoutSession = $checkoutSession;
        $this->currentPageObject = $currentPageObject;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->ruleTypeResolver = $ruleTypeResolver;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Validate product on related rule and return valid products
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return Product[]
     * @throws NoSuchEntityException
     */
    public function validateAndGetProducts($rule, $blockType)
    {
        $products = [];
        $this->productCollection->resetCollection();
        if ($this->applyToProductCollectionRuleConditionsAndSorting($rule, $blockType)) {
            $this->productCollection
                ->addStoreFilter($this->storeManager->getStore()->getId())
                ->setVisibility($this->productVisibility->getVisibleInSiteIds());
            if ($this->isNeedToHideOutOfStockProducts($rule)) {
                $this->productCollection->addInStockFilter();
            }

            $currentProductId = $this->currentPageObject->getCurrentProductIdForBlock($rule, $blockType);
            $excludedProducts = $this->getExcludedProducts($currentProductId);
            $this->productCollection->addAttributeToSelect('*');
            $this->productCollection->addAttributeToFilter('entity_id', ['nin' => $excludedProducts]);
            $this->productCollection->addUrlRewrite();

            $this->productCollection
                ->setPageSize($rule->getLimit())
                ->setCurPage(1)
                ->load();

            $products = $this->productCollection->getItems();
        }
        return $products;
    }

    /**
     * Apply to the product collection conditions and sorting according to the product condition type of the rule
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    private function applyToProductCollectionRuleConditionsAndSorting($rule, $blockType)
    {
        $wasAppliedSuccessfully = false;
        switch ($rule->getProductConditionType()) {
            case ProductConditionType::CONDITIONS_COMBINATION:
                $wasAppliedSuccessfully = $this->applyConditionsCombinationConditionAndSorting($rule, $blockType);
                break;
            case ProductConditionType::WHO_BOUGHT_THIS_ALSO_BOUGHT:
                $wasAppliedSuccessfully = $this->applyWbtabConditionAndSorting($rule, $blockType);
                break;
            case ProductConditionType::WHO_VIEWED_THIS_ALSO_VIEWED:
                $wasAppliedSuccessfully = $this->applyWvtavConditionAndSorting($rule, $blockType);
                break;
        }
        return $wasAppliedSuccessfully;
    }

    /**
     * Apply to the product collection conditions and sorting of conditions combination display mode
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    private function applyConditionsCombinationConditionAndSorting($rule, $blockType)
    {
        $wasAppliedSuccessfully = false;
        $filteredIds = $this->getFilteredIdsFromRuleConditionsCombination($rule, $blockType);
        if ((is_array($filteredIds) || $filteredIds instanceof \Countable) && count($filteredIds)) {
            $this->productCollection
                ->addIdFilter($filteredIds)
                ->addProductSorting($rule->getSortType());
            $wasAppliedSuccessfully = true;
        }
        return $wasAppliedSuccessfully;
    }

    /**
     * Apply to the product collection conditions and sorting of WBTAB display mode
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    private function applyWbtabConditionAndSorting($rule, $blockType)
    {
        $wasAppliedSuccessfully = false;
        $currentProductId = $this->currentPageObject->getCurrentProductIdForBlock($rule, $blockType);
        if (!empty($currentProductId)) {
            $currentProduct = $this->getProductInstanceById($currentProductId);
            if (!empty($currentProduct)) {
                if ($this->isArpOverridedByNativeRelated($currentProduct)) {
                    $relatedProductCollection = $this->getPreparedCollectionOfNativeRelatedForProduct($currentProduct);
                    $this->productCollection->joinNativeRelatedProducts($relatedProductCollection);
                    $this->productCollection->addNativeRelatedProductsSorting();
                    $wasAppliedSuccessfully = true;
                } else {
                    $this->productCollection->joinWbtabProducts();
                    $this->productCollection->addWbtabProductFiltering($currentProductId);
                    $this->productCollection->addWbtabProductSorting();
                    $wasAppliedSuccessfully = true;
                }
            }
        }
        return $wasAppliedSuccessfully;
    }

    /**
     * Apply to the product collection conditions and sorting of WVTAV display mode
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return bool
     */
    private function applyWvtavConditionAndSorting($rule, $blockType)
    {
        $wasAppliedSuccessfully = false;
        $currentProductId = $this->currentPageObject->getCurrentProductIdForBlock($rule, $blockType);
        if (!empty($currentProductId)) {
            $currentProduct = $this->getProductInstanceById($currentProductId);
            if (!empty($currentProduct)) {
                if ($this->isArpOverridedByNativeRelated($currentProduct)) {
                    $relatedProductCollection = $this->getPreparedCollectionOfNativeRelatedForProduct($currentProduct);
                    $this->productCollection->joinNativeRelatedProducts($relatedProductCollection);
                    $this->productCollection->addNativeRelatedProductsSorting();
                    $wasAppliedSuccessfully = true;
                } else {
                    $this->productCollection->joinWvtavProducts();
                    $this->productCollection->addWvtavProductFiltering($currentProductId);
                    $this->productCollection->addWvtavProductSorting();
                    if ($rule->getWvtavProductConditionIsSuggestOnlyOneCategory()) {
                        $this->productCollection->addWvtavOnlyOneCategoryFilter($currentProduct);
                    }
                    if ($rule->getWvtavProductConditionIsSuggestOnlyPriceHigher()) {
                        $currentProductPrice = (empty($currentProduct->getFinalPrice()))
                            ? $currentProduct->getPrice()
                            : $currentProduct->getFinalPrice();
                        $this->productCollection->addWvtavOnlyPriceHigherFilter($currentProductPrice);
                    }
                    $wasAppliedSuccessfully = true;
                }
            }
        }
        return $wasAppliedSuccessfully;
    }

    /**
     * Retrieve product ids from related product conditions
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return array
     */
    private function getFilteredIdsFromRuleConditionsCombination($rule, $blockType)
    {
        $filteredIds = [];
        if ($this->ruleTypeResolver->isRuleTypeUseCategoryRelatedProductCondition($rule->getType())) {
            $filteredIds = $this->getProductIdsFromCategoryRelatedProductRule($rule);
        } else {
            $filteredIds = $this->getProductIdsForProductAndCartBlock($rule, $blockType);
        }
        return $filteredIds;
    }

    /**
     * Retrieve product ids for rule conditions for product and cart page
     *
     * @param RuleInterface $rule
     * @param int $blockType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getProductIdsForProductAndCartBlock($rule, $blockType)
    {
        $filteredIds = [];
        $currentProductId = $this->currentPageObject->getCurrentProductIdForBlock($rule, $blockType);
        $currentCategoryId = $this->currentPageObject->getCurrentCategoryIdForBlock($rule, $blockType);
        if (!$currentProductId) {
            return $filteredIds;
        }

        $currentProduct = $this->getProductInstanceById($currentProductId);
        if (empty($currentProduct)) {
            return $filteredIds;
        }

        // Override ARP products on native related products if neccesary
        if ($this->isArpOverridedByNativeRelated($currentProduct)) {
            $relatedProductCollection = $this->getPreparedCollectionOfNativeRelatedForProduct($currentProduct);
            $relatedProductIds = $relatedProductCollection->getAllIds();
            return $relatedProductIds;
        }

        $additionalParams = $this->getAdditionalParamsForRuleValidation($currentProduct, $currentCategoryId);
        $filteredIds = $rule->getRelatedProductRule()->getMatchingProductIds($currentProductId, $additionalParams);

        return $filteredIds;
    }

    /**
     * Retrieve additional parameters for rule validation
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param int|null $currentCategoryId
     * @return array
     */
    private function getAdditionalParamsForRuleValidation($currentProduct, $currentCategoryId)
    {
        $additionalParams = [];
        if (empty($currentCategoryId)) {
            $additionalParams['category_ids'] = $this->getFilteredCategoriesIdsForProduct($currentProduct);
        } else {
            $additionalParams['category_ids'] = [$currentCategoryId];
        }
        return $additionalParams;
    }

    /**
     * Retrieve more relevant categories for product
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @return array
     */
    private function getFilteredCategoriesIdsForProduct($currentProduct)
    {
        $filteredCategories = [];
        $currentCategoriesIds = $currentProduct->getCategoryIds();
        foreach ($currentCategoriesIds as $categoryId) {
            if ($this->isCategoryHasMoreRelevantChildForProduct($categoryId, $currentProduct)) {
                continue;
            }
            $filteredCategories[] = $categoryId;
        }
        return $filteredCategories;
    }

    /**
     * Check if product is assigned to the subcategory of the current one
     *
     * @param int $categoryId
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    private function isCategoryHasMoreRelevantChildForProduct($categoryId, $product)
    {
        $result = false;
        $currentCategoriesIds = $product->getCategoryIds();
        try {
            $category = $this->categoryRepository->get($categoryId);
            if ($category->hasChildren()) {
                $childrenCategoriesIds = $category->getAllChildren(true);
                $key = array_search($categoryId, $childrenCategoriesIds);
                if ($key == false) {
                    unset($childrenCategoriesIds[$key]);
                }
                $intersect = array_intersect($currentCategoriesIds, $childrenCategoriesIds);
                if (count($intersect)) {
                    $result = true;
                }
            }
        } catch (NoSuchEntityException $e) {
            $result = true;
        }
        return $result;
    }

    /**
     * Retrieve product ids from category related rule conditions
     *
     * @param RuleInterface $rule
     * @return array
     */
    private function getProductIdsFromCategoryRelatedProductRule($rule)
    {
        $productIds = $rule->getRelatedProductRule()->getMatchingProductIds();

        return $productIds;
    }

    /**
     * Get products ids for excludes from related products
     *
     * @param int $productId
     * @return array
     */
    private function getExcludedProducts($productId)
    {
        $productIds = [$productId];

        $quoteModel = $this->checkoutSession->getQuote();
        if (!$quoteModel->getId()) {
            return $productIds;
        }

        foreach ($quoteModel->getItemsCollection() as $item) {
            $productIds[] = $item->getProductId();
        }

        return $productIds;
    }

    /**
     * Check is need to hide out of stock related products
     *
     * @param RuleInterface $rule
     * @return bool
     */
    private function isNeedToHideOutOfStockProducts($rule)
    {
        return ($rule->getIsDisplayOutofstock() != true);
    }

    /**
     * Retrieve product instance by id
     *
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function getProductInstanceById($productId)
    {
        try {
            $productInstance = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $productInstance = null;
        }
        return $productInstance;
    }

    /**
     * Check if need to override ARP products on native related products
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $productInstance
     * @return bool
     */
    private function isArpOverridedByNativeRelated($productInstance)
    {
        return (bool)$productInstance->getAwArpOverrideNative();
    }

    /**
     * Retrieve prepared collection of native related product for specified product instance
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $productInstance
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    private function getPreparedCollectionOfNativeRelatedForProduct($productInstance)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $relatedProductCollection */
        $relatedProductCollection = $productInstance->getRelatedProductCollection();
        $relatedProductCollection->addAttributeToSelect('required_options')
            ->setPositionOrder()
            ->addStoreFilter()
            ->load()
        ;
        return $relatedProductCollection;
    }
}
