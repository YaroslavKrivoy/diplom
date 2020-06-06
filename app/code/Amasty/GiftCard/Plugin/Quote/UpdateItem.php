<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Quote;

use Amasty\GiftCard\Model\Product\Type\GiftCard;

class UpdateItem
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    public function __construct(\Magento\Catalog\Model\ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * save type of gift card
     *
     * @param \Magento\Quote\Model\Quote $subject
     * @param callable $proceed
     * @param int $itemId
     * @param \Magento\Framework\DataObject $buyRequest
     * @param null|array|\Magento\Framework\DataObject $params
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\TemporaryState\CouldNotSaveException
     */
    public function beforeUpdateItem($subject, $itemId, $buyRequest, $params = null)
    {
        $item = $subject->getItemById($itemId);
        if($item) {
            $productId = $item->getProduct()->getId();
            $product = $this->productRepository->getById($productId);
            if ($product->getTypeId() === GiftCard::TYPE_GIFTCARD_PRODUCT) {
                $giftCardType = $buyRequest->getAmGiftcardType();
                if ($giftCardType) {
                    $product->setAmGiftCardType($giftCardType);
                    $this->productRepository->save($product);
                }
            }
        }
    }
}
