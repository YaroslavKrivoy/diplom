<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Quote;

use Amasty\Base\Model\Serializer;
use Amasty\GiftCard\Model\GiftCard;
use Amasty\GiftCard\Model\Product\Type\GiftCard as GiftCardType;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Item
{
    private $price = [];

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(Serializer $serializer, ProductRepositoryInterface $productRepository)
    {
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param $price
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConvertedPrice(
        \Magento\Quote\Model\Quote\Item $item,
        $price
    ) {
        $product = $item->getProduct();
        if ($product->getTypeId() == GiftCardType::TYPE_GIFTCARD_PRODUCT) {
            $itemId = $item->getId();
            if ($this->price && isset($this->price[$itemId]) && $this->price[$itemId] == $price) {
                return $price;
            }
            if (isset($item->getOptionsByCode()['info_buyRequest'])
                && isset($item->getOptionsByCode()['info_buyRequest']['value'])
            ) {
                $options = $this->serializer->unserialize($item->getOptionsByCode()['info_buyRequest']['value']);
                $price = $this->getResultPrice($options, $product);
                $this->price += [$itemId => $price];
            }
        }

        return $price;
    }

    /**
     * @param $options
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getResultPrice($options, $product)
    {
        $price = $this->getPriceByOption($options);
        $resultPrice = $this->getPriceByFee($product, $price);

        return $resultPrice;
    }

    /**
     * @param $options
     * @return int|mixed
     */
    private function getPriceByOption($options)
    {
        if (isset($options['am_giftcard_amount_custom']) && $options['am_giftcard_amount_custom']) {
            $price = $options['am_giftcard_amount_custom'];
        } else {
            $price = isset($options['am_giftcard_amount']) ? $options['am_giftcard_amount'] : null;
        }

        return $price;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param $price
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPriceByFee($product, $price)
    {
        $feeType = $product->getAmGiftcardFeeType();
        /*missing gift card products options on checkout cart*/
        if ($feeType == null) {
            $product = $this->productRepository->getById($product->getId());
            $feeType = $product->getAmGiftcardFeeType();
        }

        $feeValue = (float)$product->getAmGiftcardFeeValue();
        if ($feeType == GiftCard::PRICE_TYPE_PERCENT) {
            $price += $price * $feeValue / 100;
        } elseif ($feeType == GiftCard::PRICE_TYPE_FIXED) {
            $price += $feeValue;
        }

        return $price;
    }
}
