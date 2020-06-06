<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin;

use Magento\Quote\Model\Quote;
use Magento\Framework\App\RequestInterface;

class BeforeCollectTotalsPlugin extends AbstractPlugin
{
    const BLOCK_INFO             = 'info';
    const BLOCK_ACCOUNT          = 'account';
    const BLOCK_BILLING_ADDRESS  = 'billing_address';
    const BLOCK_SHIPPING_ADDRESS = 'shipping_address';
    const BLOCK_ORDER_ITEMS      = 'order_items';
    const BLOCK_SHIPPING_METHOD  = 'shipping_method';
    const BLOCK_PAYMENT_METHOD   = 'payment_method';

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
     * @param \Magento\GiftCardAccount\Model\Plugin\TotalsCollector $object
     * @param callable $proceed
     * @param Quote\TotalsCollector $subject
     * @param Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundBeforeCollect(
        \Magento\GiftCardAccount\Model\Plugin\TotalsCollector $object,
        callable $proceed,
        \Magento\Quote\Model\Quote\TotalsCollector $subject,
        Quote $quote
    ) {
        if ($this->isOrderEdit()) {
            $quote->setBaseGiftCardsAmountUsed(0);
            $quote->setGiftCardsAmountUsed(0);
        } else {
            $proceed($subject, $quote);
        }
    }
}