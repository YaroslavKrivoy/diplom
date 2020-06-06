<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Shipping;

use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form as ShippingMethodForm;

class Method extends ShippingMethodForm
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Retrieve current selected shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getOrder()->getShippingMethod();
    }

    /**
     * @param Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return float
     */
    public function getCurrentShippingPrice()
    {
        return $this->getOrder()->getShippingAmount();
    }

    /**
     * @return float
     */
    public function getCurrentShippingPriceInclTax()
    {
        return $this->order->getShippingAmount() + $this->order->getShippingTaxAmount();
    }

    /**
     * @param float $price
     * @param bool|null $flag
     * @return float
     */
    public function getShippingPriceFloat($price, $flag)
    {
        return $this->_taxData->getShippingPrice(
            $price,
            $flag,
            $this->getAddress(),
            null,
            $this->getAddress()->getQuote()->getStore()
        );
    }
}
