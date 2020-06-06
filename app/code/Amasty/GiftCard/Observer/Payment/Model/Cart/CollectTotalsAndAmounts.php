<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Observer\Payment\Model\Cart;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CollectTotalsAndAmounts implements ObserverInterface
{

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $giftQuoteCollectionFactory;

    public function __construct(\Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $giftQuoteCollectionFactory)
    {
        $this->giftQuoteCollectionFactory = $giftQuoteCollectionFactory;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Paypal\Model\Cart $cart */
        $cart = $observer->getCart();
        $id = $cart->getSalesModel()->getDataUsingMethod('entity_id');
        if (!$id) {
            $id = $cart->getSalesModel()->getDataUsingMethod('quote_id');
        }

        $giftsQuoteCollection = $this->giftQuoteCollectionFactory->create()->getGiftCardsByQuoteId($id);

        $baseGiftAmount = 0;

        foreach ($giftsQuoteCollection as $giftQuote) {
            $baseGiftAmount += $giftQuote->getGiftAmount();
        }
        if ($baseGiftAmount > 0) {
            $cart->addCustomItem(
                'Gift Card',
                1,
                -$baseGiftAmount
            );
        }
    }
}
