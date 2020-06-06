<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Observer\Address;

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
        /** @var \Magento\Quote\Model\Quote\Address\Total $totals */
        $totals = $observer->getTotal();
        $currencyRate = $this->store->getCurrentCurrencyRate();
        if ($totals->getSubtotal() != 0 && $totals->getBaseSubtotal() == 0) {
            $totals->setBaseSubtotal($totals->getSubtotal() / $currencyRate);
        }
        if ($totals->getGrandTotal() != 0 && $totals->getBaseGrandTotal() == 0) {
            $totals->setBaseGrandTotal($totals->getGrandTotal() / $currencyRate);
        }
        if ($totals->getSubtotalWithDiscount() != 0 && $totals->getBaseSubtotalWithDiscount() == 0) {
            $totals->setBaseSubtotalWithDiscount($totals->getSubtotalWithDiscount() / $currencyRate);
        }

        return [$observer->getQuote(), $observer->getShippingAssignment(), $totals];
    }
}
