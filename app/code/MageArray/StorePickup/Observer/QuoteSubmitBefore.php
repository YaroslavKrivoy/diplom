<?php

namespace MageArray\StorePickup\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \MageArray\StorePickup\Model\StoreFactory $storeFactory,
        \MageArray\StorePickup\Helper\Data $dataHelper
    ) {
        $this->_objectManager = $objectmanager;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeFactory = $storeFactory;
        $this->dataHelper = $dataHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $enable = $this->dataHelper->isEnabled();
        if ($enable == 1) {
            if ($order->getShippingMethod()) {
                if ($order->getShippingMethod(true)->getCarrierCode() == "storepickup") {
                    $quoteRepository = $this->_objectManager
                        ->create('Magento\Quote\Model\QuoteRepository');
                    $quote = $quoteRepository->get($order->getQuoteId());
                    $order->setPickupDate($quote->getPickupDate());
                    $order->setPickupStore($quote->getPickupStore());
                    $order->setPickupTime($quote->getPickupTime());
                }
            }
        }
    }
}
