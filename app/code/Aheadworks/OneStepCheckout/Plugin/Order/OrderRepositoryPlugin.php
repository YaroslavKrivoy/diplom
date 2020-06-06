<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Plugin\Order;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;

/**
 * Class OrderRepositoryPlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Order
 */
class OrderRepositoryPlugin
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Add extension attributes to order
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $this->setExtensionAttributes($order);
        return $order;
    }

    /**
     * Set extension attributes to order entity
     *
     * @param OrderInterface $order
     */
    private function setExtensionAttributes(OrderInterface $order)
    {
        /** @var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $extensionAttributes->setAwDeliveryDate($order->getAwDeliveryDate());
        $extensionAttributes->setAwDeliveryDateFrom($order->getAwDeliveryDateFrom());
        $extensionAttributes->setAwDeliveryDateTo($order->getAwDeliveryDateTo());
        $extensionAttributes->setAwOrderNote($order->getAwOrderNote());

        $order->setExtensionAttributes($extensionAttributes);
    }
}
