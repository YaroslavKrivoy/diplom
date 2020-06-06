<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Observer\Order\Email;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\OneStepCheckout\Model\Order\Email\VariableProcessor as OrderVariableProcessor;
use Magento\Sales\Model\Order;

/**
 * Class VariableObserver
 * @package Aheadworks\OneStepCheckout\Observer\Order\Email
 */
class VariableObserver implements ObserverInterface
{
    /**
     * @var OrderVariableProcessor
     */
    private $variableProcessor;

    /**
     * @param OrderVariableProcessor $variableProcessor
     */
    public function __construct(
        OrderVariableProcessor $variableProcessor
    ) {
        $this->variableProcessor = $variableProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if ($order = $this->getOrder($observer)) {
            $this->variableProcessor->addDeliveryDateVariables($order);
        }

        return $this;
    }

    /**
     * Retrieve order from observer
     *
     * @param Observer $observer
     * @return Order|null
     */
    private function getOrder(Observer $observer)
    {
        $order = null;
        $transportObject = $observer->getData('transportObject');
        $transport = $observer->getData('transport');

        if (is_object($transportObject)) {
            $order = $transportObject->getOrder();
        }
        if (!isset($order) && is_object($transport)) {
            $order = $transport->getOrder();
        }
        return $order;
    }
}
