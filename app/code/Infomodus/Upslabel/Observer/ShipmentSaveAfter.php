<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Observer;

use Magento\Framework\Event\ObserverInterface;

class ShipmentSaveAfter implements ObserverInterface
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
    }
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        /*$quote = $observer->getEvent()->getQuote();*/

       $shipment = $observer->getEvent()->getShipment();
        if ($this->_coreRegistry->registry('upslabel_order_id') === null) {
            $this->_coreRegistry->register('upslabel_order_id', $shipment->getOrder()->getId());
            $this->_coreRegistry->register('upslabel_shipment_id', $shipment->getId());
        }
    }
}
