<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin;

use Magento\Framework\App\RequestInterface;

class CollectCustomerBalancePlugin extends AbstractPlugin
{
    /**
     * BeforeCollectTotalsPlugin constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        parent::__construct($request);
    }

    /**
     * @param \Magento\CustomerBalance\Model\Total\Quote\Customerbalance $object
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function aroundCollect(
        \Magento\CustomerBalance\Model\Total\Quote\Customerbalance $object,
        callable $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        if (!$this->isOrderEdit()) {
            $proceed($quote, $shippingAssignment, $total);
        }

        return $this;
    }
}