<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Admin\Items\Render;

use Amasty\GiftCard\Model\Product\Type\GiftCard;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ItemHtml
{
    const PRICE_FIELDS = [
        'base_price',
        'base_row_total',
        'base_original_price',
        'price',
        'original_price',
    ];

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject
     * @param \Magento\Framework\DataObject $item
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeGetItemHtml(
        \Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject,
        \Magento\Framework\DataObject $item
    ) {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $product = $this->productRepository->getById($item->getProductId());
        } catch (NoSuchEntityException $entityException) {
            $product = $this->productFactory->create();
        }

        if ($product->getTypeId() === GiftCard::TYPE_GIFTCARD_PRODUCT) {
            $this->fixShowItemPrice($item);
        }

        return [$item];
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     */
    private function fixShowItemPrice($item)
    {
        $price = $item->getPrice();
        $orderItem = $item->getOrderItem();
        if ($orderItem) {
            $basePrice = $price / $orderItem->getOrder()->getBaseToOrderRate();
        } else {
            $basePrice = $price / $item->getOrder()->getBaseToOrderRate();
        }
        foreach (self::PRICE_FIELDS as $priceField) {
            if ($orderItem) {
                $orderItem->setData($priceField, $basePrice);
            }
            if (strpos($priceField, 'base') !== false) {
                $item->setData($priceField, $basePrice);
            } else {
                $item->setData($priceField, $price);
            }
        }
    }
}
