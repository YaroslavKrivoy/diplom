<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\Store;

class Totals extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals
{
    /**
     * Address form template
     *
     * @var string
     */
    protected $_template = 'edit/totals.phtml';

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $modelTaxConfig;

    const DISPLAY_EXCLUDING_TAX = 1;
    const DISPLAY_INCLUDING_TAX = 2;
    const DISPLAY_BOTH          = 3;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param \Magento\Tax\Model\Config $modelTaxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Tax\Model\Config $modelTaxConfig,
        array $data = []
    ) {
        $this->helperData     = $helperData;
        $this->modelTaxConfig = $modelTaxConfig;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $salesData,
            $salesConfig,
            $data
        );
    }

    /**
     * Retrieve order model object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->helperData->getOrder();
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->helperData->getCustomerId();
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->helperData->getStore();
    }

    /**
     * Retrieve store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->helperData->getStoreId();
    }

    /**
     * Get totals
     *
     * @return array
     */
    public function getTotals()
    {
        $totals = $this->getQuote()->getTotals();
        foreach ($totals as $total) {
            if ($total->getCode() == 'shipping' && !$total->getValue()) {
                $total->setValue($this->getQuote()->getShippingAddress()->getShippingAmount());
            }
        }

        return $totals;
    }

    /**
     * Has discount
     *
     * @return bool
     */
    public function hasDiscount()
    {
        $hasDiscount = false;
        foreach ($this->getTotals() as $total) {
            if ($total->getCode() == 'discount') {
                $hasDiscount = true;
                break;
            }
        }

        return $hasDiscount;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasStoreCredit()
    {
        return (bool)$this->getOrder()->getCustomerBalanceAmount();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreCredit()
    {
        return $this->getOrder()->getCustomerBalanceAmount();
    }

    /**
     * @param null|int|string|Store $store $store
     * @return bool
     */
    public function isShowSubtotalExclTax($store)
    {
        if ($this->modelTaxConfig->displaySalesSubtotalExclTax($store)
            || $this->modelTaxConfig->displaySalesSubtotalBoth($store)) {
            return true;
        }

        return false;
    }

    /**
     * @param null|int|string|Store $store $store
     * @return bool
     */
    public function isShowSubtotalInclTax($store)
    {
        if ($this->modelTaxConfig->displaySalesSubtotalInclTax($store)
            || $this->modelTaxConfig->displaySalesSubtotalBoth($store)) {
            return true;
        }

        return false;
    }

    /**
     * @param null|int|string|Store $store $store
     * @return bool
     */
    public function isShowShippingExclTax($store)
    {
        if ($this->modelTaxConfig->displaySalesShippingExclTax($store)
            || $this->modelTaxConfig->displaySalesShippingBoth($store)) {
            return true;
        }

        return false;
    }

    /**
     * @param null|int|string|Store $store $store
     * @return bool
     */
    public function isShowShippingInclTax($store)
    {
        if ($this->modelTaxConfig->displaySalesShippingInclTax($store)
            || $this->modelTaxConfig->displaySalesShippingBoth($store)) {
            return true;
        }

        return false;
    }

    /**
     * Get subtotal tax amount
     *
     * @return float
     */
    public function getSubtotalTaxAmount()
    {
        // @TODO: bug! Check it from checkout
        // @TODO Temporary fixed:
        return $this->getOrder()->getTaxAmount() - $this->getOrder()->getShippingTaxAmount();
    }

    /**
     * Format value according to default precision
     *
     * @return float
     */
    public function format($value)
    {
        $priceCurrency = $this->priceCurrency;
        $precision     = $priceCurrency::DEFAULT_PRECISION;

        return number_format($value, $precision);
    }
}
