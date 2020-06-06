<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Model\Options as Options;
use Amasty\GiftCard\Model\GiftCardOptionsConverter as GiftCardOptionsConverter;
use Amasty\GiftCard\Model\Product\Type\GiftCard as amGiftCard;
use Amasty\GiftCard\Api\Data\AmGiftCardOptionsInterfaceFactory as AmGiftCardOptionsInterfaceFactory;
use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data as QuoteApi;
use Magento\Framework\DataObject;

class CartItemProcessor implements CartItemProcessorInterface
{
    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    /**
     * @var QuoteApi\ProductOptionExtensionFactory
     */
    private $productOptionExtensionFactory;

    /**
     * @var AmGiftCardOptionsInterfaceFactory
     */
    private $amGiftCardOptionsFactory;

    /**
     * @var QuoteApi\ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @var GiftCardOptionsConverter
     */
    private $giftCardOptionsConverter;

    public function __construct(
        \Magento\Framework\DataObject\Factory $objectFactory,
        QuoteApi\ProductOptionExtensionFactory $productOptionExtensionFactory,
        AmGiftCardOptionsInterfaceFactory $amGiftCardOptionsFactory,
        QuoteApi\ProductOptionInterfaceFactory $productOptionFactory,
        GiftCardOptionsConverter $giftCardOptionsConverter
    ) {
        $this->objectFactory = $objectFactory;
        $this->productOptionExtensionFactory = $productOptionExtensionFactory;
        $this->amGiftCardOptionsFactory = $amGiftCardOptionsFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->giftCardOptionsConverter = $giftCardOptionsConverter;
    }

    /**
     * @param CartItemInterface $cartItem
     * @return DataObject|null
     */
    public function convertToBuyRequest(CartItemInterface $cartItem)
    {
        if ($cartItem->getProductOption() && $cartItem->getProductOption()->getExtensionAttributes()) {
            $options = $cartItem->getProductOption()->getExtensionAttributes()->getAmgiftcardOptions();
            if ($options) {
                $requestData = $this->giftCardOptionsConverter->prepareToBuyRequest($options);
                return $this->objectFactory->create($requestData);
            }
        }
        return null;
    }

    /**
     * @param CartItemInterface $cartItem
     * @return CartItemInterface
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        if ($cartItem->getProductType() !== AmGiftCard::TYPE_GIFTCARD_PRODUCT) {
            return $cartItem;
        }

        $requestData = $cartItem->getBuyRequest()->getData();
        /** @var Options $productOption */
        $productOption = $this->amGiftCardOptionsFactory->create();
        $this->giftCardOptionsConverter->prepareToProductOption($requestData, $productOption);

        $extension = $this->productOptionExtensionFactory->create()->setAmgiftcardOptions($productOption);

        if (!$cartItem->getProductOption()) {
            $cartItem->setProductOption($this->productOptionFactory->create());
        }

        $cartItem->getProductOption()->setExtensionAttributes($extension);

        return $cartItem;
    }
}
