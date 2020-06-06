<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Observer\Quote;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Totals implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    public function __construct(\Magento\Store\Model\Store $store)
    {
        $this->store = $store;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        $currencyRate = $this->store->getCurrentCurrencyRate();

        switch ($quote) {
            case $quote->getBaseSubtotal() == 0:
                $quote->setBaseSubtotal($quote->getSubtotal() / $currencyRate);
            case $quote->getBaseGrandTotal() == 0:
                $quote->setBaseGrandTotal($quote->getGrandTotal() / $currencyRate);
            case $quote->getBaseSubtotalWithDiscount() == 0:
                $quote->setBaseSubtotalWithDiscount($quote->getSubtotalWithDiscount() / $currencyRate);
        }

        return $quote;
    }
}
