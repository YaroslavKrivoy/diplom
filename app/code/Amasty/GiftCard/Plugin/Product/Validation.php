<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Product;

use Amasty\GiftCard\Model\Product\Type\GiftCard;

class Validation
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    private $gifCardHelper;

    /**
     * Validation constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Amasty\GiftCard\Helper\Data $gifCardHelper
    ) {
        $this->productRepository = $productRepository;
        $this->gifCardHelper = $gifCardHelper;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Product $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundValidate(
        \Magento\SalesRule\Model\Rule\Condition\Product $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $model
    ) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $model->getProduct();
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $product = $this->productRepository->getById($model->getProductId());
        }

        if ($product->getTypeId() == GiftCard::TYPE_GIFTCARD_PRODUCT) {
            $gifCardPrice = $this->gifCardHelper->getFirstOfAmount($product);
            $product->setPrice($gifCardPrice);
        }

        $model->setProduct($product);

        return $proceed($model);
    }
}
