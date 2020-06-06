<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Order;

use Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderRepository as MagentoOrderRepository;

class OrderRepository
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var CollectionFactory
     */
    private $giftCollectionFactory;

    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        CollectionFactory $giftCollectionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->giftCollectionFactory = $giftCollectionFactory;
    }

    /**
     * @param MagentoOrderRepository   $subject
     * @param OrderInterface    $order
     *
     * @return OrderInterface
     */
    public function afterGet(MagentoOrderRepository $subject, OrderInterface $order)
    {
        $this->loadGiftCardExtensionAttributes($order);

        return $order;
    }

    /**
     * @param MagentoOrderRepository               $subject
     * @param OrderSearchResultInterface    $orderCollection
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(MagentoOrderRepository $subject, OrderSearchResultInterface $orderCollection)
    {
        foreach ($orderCollection->getItems() as $order) {
            $this->loadGiftCardExtensionAttributes($order);
        }

        return $orderCollection;
    }

    /**
     * @param OrderInterface $order
     */
    private function loadGiftCardExtensionAttributes(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }
        if ($extensionAttributes->getAmGiftCardCode() !== null) {
            // Delivery Date entity is already loaded; no actions required
            return;
        }
        try {
            $giftsCardQuote = $this->giftCollectionFactory->create()
                ->getGiftCardsWithAccount($order->getQuoteId());
            $codeSetIds = [];
            $accountIds = [];
            $giftCoupons = [];
            $giftAmount = 0;
            $baseGiftAmount = 0;

            foreach ($giftsCardQuote as $giftCard) {
                $codeSetIds[] = (int)$giftCard->getCodeSetId();
                $accountIds[] = (int)$giftCard->getAccountId();
                $giftCoupons[] = (string)$giftCard->getCode();
                $giftAmount += (float)$giftCard->getGiftAmount();
                $baseGiftAmount += (float)$giftCard->getBaseGiftAmount();
            }

            $extensionAttributes->setAmgiftcardCodeSetId(implode(array_unique($codeSetIds), ","));
            $extensionAttributes->setAmgiftcardAccountId(implode(array_unique($accountIds), ","));
            $extensionAttributes->setAmGiftcardCode(implode(array_unique($giftCoupons), ","));
            $extensionAttributes->setAmgiftcardGiftAmount($giftAmount);
            $extensionAttributes->setAmgiftcardBaseGiftAmount($baseGiftAmount);

            $order->setExtensionAttributes($extensionAttributes);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Delivery Date entity cannot be loaded for current order; no actions required
            return;
        }
    }
}
