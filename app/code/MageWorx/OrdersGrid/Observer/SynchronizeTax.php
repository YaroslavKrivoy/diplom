<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Address;
use MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\Collection;

/**
 * Class Synchronize Tax
 * @package MageWorx\OrdersGrid\Observer
 *
 * Observer class for the automatically synchronization of additional table of the orders grid for the Tax
 *
 * @see \MageWorx\OrdersGrid\Observer\Synchronize::CLASS_MAPPER
 */
class SynchronizeTax implements ObserverInterface
{
    /**
     * @var Collection
     */
    private $customOrderGridCollection;

    /**
     * SynchronizeTax constructor.
     * @param Collection $customOrderGridCollection
     */
    public function __construct(
        Collection $customOrderGridCollection
    ) {
        $this->customOrderGridCollection = $customOrderGridCollection;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Tax\Model\Sales\Order\Tax $object */
        $object = $event->getDataObject();
        if (!is_object($object) || !($object instanceof \Magento\Tax\Model\Sales\Order\Tax)) {
            return $this;
        }

        $orderId = $object->getOrderId();
        if (!$orderId) {
            return $this;
        }
        $this->customOrderGridCollection->grabDataFromSalesOrderTaxTable([$orderId]);

        return $this;
    }
}
