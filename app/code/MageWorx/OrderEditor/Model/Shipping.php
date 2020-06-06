<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use Magento\Framework\Model\AbstractModel;

class Shipping extends AbstractModel
{
    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $shippingMethod;

    /**
     * @var string
     */
    protected $shippingDescription;

    /**
     * @var float
     */
    protected $shippingPrice;

    /**
     * @var float
     */
    protected $shippingPriceInclTax;

    /**
     * @var float
     */
    protected $taxPercent;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var \Magento\Shipping\Model\Shipping
     */
    protected $shipping;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateFactory
     */
    protected $_addressRateFactory;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $customerGroup;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest
     */
    protected $shippingRateRequest;

    /**
     * @var null
     */
    protected $rate;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory
     */
    protected $_rateCollectionFactory;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param Order $order
     * @param Quote $quote
     * @param \Magento\Shipping\Model\Shipping $shipping
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Customer\Model\Group $customerGroup
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $shippingRateRequest
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        Order $order,
        Quote $quote,
        \Magento\Shipping\Model\Shipping $shipping,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Customer\Model\Group $customerGroup,
        \Magento\Quote\Model\Quote\Address\RateRequest $shippingRateRequest,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->order = $order;
        $this->quote = $quote;
        $this->shipping = $shipping;
        $this->directoryHelper = $directoryHelper;
        $this->_addressRateFactory = $addressRateFactory;
        $this->helperData = $helperData;
        $this->taxConfig = $taxConfig;
        $this->taxCalculation = $taxCalculation;
        $this->customerGroup = $customerGroup;
        $this->shippingRateRequest = $shippingRateRequest;
        $this->rate = null;
        $this->quoteRepository = $quoteRepository;
        $this->_rateCollectionFactory = $rateCollectionFactory;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->helperData->getOrder();
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingDescription()
    {
        return $this->shippingDescription;
    }

    /**
     * @param string $shippingDescription
     * @return $this
     */
    public function setShippingDescription($shippingDescription)
    {
        $this->shippingDescription = $shippingDescription;
        return $this;
    }

    /**
     * @param float $shippingPrice
     * @return $this
     */
    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
        return $this;
    }

    /**
     * @return float
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * @param float $shippingPriceInclTax
     * @return $this
     */
    public function setShippingPriceInclTax($shippingPriceInclTax)
    {
        $this->shippingPriceInclTax = $shippingPriceInclTax;
        return $this;
    }

    /**
     * @return float
     */
    public function getShippingPriceInclTax()
    {
        return $this->shippingPriceInclTax;
    }

    /**
     * @param float $taxPercent
     * @return $this
     */
    public function setTaxPercent($taxPercent)
    {
        $this->taxPercent = $taxPercent;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->taxPercent;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount)
    {
        return $this->setData('discount_amount', (float)$amount);
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return (float)$this->getData('discount_amount');
    }

    /**
     * @param [] $params
     * @return void
     */
    public function initParams($params)
    {
        if (isset($params['order_id'])) {
            $this->setOrderId($params['order_id']);
        }
        if (isset($params['shipping_method'])) {
            $this->setShippingMethod($params['shipping_method']);
        }
        if (isset($params['price_excl_tax'])) {
            $this->setShippingPrice($params['price_excl_tax']);
        }
        if (isset($params['price_incl_tax'])) {
            $this->setShippingPriceInclTax($params['price_incl_tax']);
        }
        if (isset($params['tax_percent'])) {
            $this->setTaxPercent($params['tax_percent']);
        }
        if (isset($params['description'])) {
            $this->setShippingDescription($params['description']);
        }
        if (isset($params['discount_amount'])) {
            $this->setDiscountAmount($params['discount_amount']);
        }
    }

    /**
     * @return void
     */
    public function updateShippingMethod()
    {
        $this->loadOrder();

        $baseShippingInclTax = $this->getShippingPriceInclTax();
        $baseShippingAmount = $this->getShippingPrice();
        $baseShippingTaxAmount = $baseShippingInclTax - $baseShippingAmount;
        $baseShippingDiscountAmount = $this->getDiscountAmount();

        /* convert currency */
        $baseCurrencyCode = $this->order->getBaseCurrencyCode();
        $orderCurrencyCode = $this->order->getOrderCurrencyCode();
        if ($baseCurrencyCode === $orderCurrencyCode) {
            $shippingAmount = $baseShippingAmount;
            $shippingInclTax = $baseShippingInclTax;
            $shippingTaxAmount = $baseShippingTaxAmount;
            $shippingDiscountAmount = $baseShippingDiscountAmount;
        } else {
            $shippingAmount = $this->directoryHelper->currencyConvert(
                $baseShippingAmount,
                $baseCurrencyCode,
                $orderCurrencyCode
            );
            $shippingInclTax = $this->directoryHelper->currencyConvert(
                $baseShippingInclTax,
                $baseCurrencyCode,
                $orderCurrencyCode
            );
            $shippingTaxAmount = $this->directoryHelper->currencyConvert(
                $baseShippingTaxAmount,
                $baseCurrencyCode,
                $orderCurrencyCode
            );
            $shippingDiscountAmount = $this->directoryHelper->currencyConvert(
                $baseShippingDiscountAmount,
                $baseCurrencyCode,
                $orderCurrencyCode
            );
        }

        // Recalculate discount amount based on shipping discount amount
        $orderDiscountAmountOld = abs($this->order->getDiscountAmount());
        $orderBaseDiscountAmountOld = abs($this->order->getBaseDiscountAmount());
        $shippingDiscountAmountOld = abs($this->order->getShippingDiscountAmount());
        $shippingBaseDiscountAmountOld = abs($this->order->getBaseShippingDiscountAmount());
        if ($shippingDiscountAmountOld != $shippingDiscountAmount) {
            $orderDiscountAmountNew = $orderDiscountAmountOld - $shippingDiscountAmountOld + $shippingDiscountAmount;
            $orderBaseDiscountAmountOld = $orderBaseDiscountAmountOld
                - $shippingBaseDiscountAmountOld
                + $baseShippingDiscountAmount;
            $this->order
                ->setDiscountAmount($orderDiscountAmountNew)
                ->setBaseDiscountAmount($orderBaseDiscountAmountOld)
                ->setShippingDiscountAmount($shippingDiscountAmount)
                ->setBaseShippingDiscountAmount($baseShippingDiscountAmount);
        }

        $this->order
            ->setShippingDescription($this->getShippingDescription())
            ->setData('shipping_method', $this->getShippingMethod())
            ->setShippingAmount($shippingAmount)
            ->setBaseShippingAmount($baseShippingAmount)
            ->setShippingInclTax($shippingInclTax)
            ->setBaseShippingInclTax($baseShippingInclTax)
            ->setShippingTaxAmount($shippingTaxAmount)
            ->setBaseShippingTaxAmount($baseShippingTaxAmount)
            ->save();

        $this->order->calculateGrandTotal();
        $this->order->updatePayment();
        $this->order->save();
    }

    /**
     * @return void
     */
    protected function loadOrder()
    {
        $id = $this->getOrderId();
        $this->order->load($id);
    }

    /**
     * @return void
     */
    public function recollectShippingAmount()
    {
        $this->loadOrder();
        $this->recollectStandardShippingMethod();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function recollectStandardShippingMethod()
    {
        $rate = $this->getCurrentShippingRate();
        if (!$rate) {
            throw new \Exception('Can not load rates');
        }
        $basePrice = $rate->getPrice();
        $price = $this->directoryHelper->currencyConvert(
            $rate->getPrice(),
            $this->order->getBaseCurrencyCode(),
            $this->order->getOrderCurrencyCode()
        );

        $this->collectShipping($price, $basePrice);
    }

    /**
     * @param float $shippingAmount
     * @param float $baseShippingAmount
     * @return float
     */
    protected function collectShipping($shippingAmount, $baseShippingAmount)
    {
        $store = $this->order->getStore();

        $shippingTaxClass = $this->taxConfig->getShippingTaxClass($store);
        $shippingPriceIncludesTax = $this->taxConfig->shippingPriceIncludesTax($store);
        $shippingTaxAmount = 0;
        $baseShippingTaxAmount = 0;

        if ($shippingTaxClass) {
            $rateRequest = $this->getRateRequest()->setProductClassId($shippingTaxClass);
            $rate = $this->taxCalculation->getRate($rateRequest);

            if ($rate) {
                if ($shippingPriceIncludesTax) {
                    $shippingTaxAmount = $shippingAmount - $shippingAmount / (1 + $rate / 100);
                } else {
                    $shippingTaxAmount = $shippingAmount * (1 + $rate / 100) - $shippingAmount;
                }
                $shippingTaxAmount = $this->helperData->roundAndFormatPrice($shippingTaxAmount);
                $this->order->setTaxAmount($this->order->getTaxAmount() + $shippingTaxAmount);

                $baseShippingTaxAmount = $baseShippingAmount - $baseShippingAmount / (1 + $rate / 100);
                $baseShippingTaxAmount = $this->helperData->roundAndFormatPrice($baseShippingTaxAmount);
                $this->order->setBaseTaxAmount($this->order->getBaseTaxAmount() + $baseShippingTaxAmount);
            }
        }

        if ($shippingPriceIncludesTax) {
            $baseShippingInclTax = $baseShippingAmount;
            $baseShippingAmount = $baseShippingAmount - $baseShippingTaxAmount;
            $shippingInclTax = $shippingAmount;
            $shippingAmount = $shippingAmount - $shippingTaxAmount;
        } else {
            $baseShippingInclTax = $baseShippingAmount + $baseShippingTaxAmount;
            $baseShippingAmount = $baseShippingAmount;
            $shippingInclTax = $shippingAmount + $shippingTaxAmount;
            $shippingAmount = $shippingAmount;
        }

        $this->order
            ->setShippingInclTax($shippingInclTax)
            ->setBaseShippingInclTax($baseShippingInclTax)
            ->setShippingTaxAmount($shippingTaxAmount)
            ->setBaseShippingTaxAmount($baseShippingTaxAmount)
            ->setShippingAmount($shippingAmount)
            ->setBaseShippingAmount($baseShippingAmount)
            ->save();

        return $baseShippingInclTax;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function getRateRequest()
    {
        $store = $this->order->getStore();
        $customerTaxClassId = $this->order->getCustomer()->getTaxClassId();
        $shippingAddress = $this->order->getShippingAddress();
        $billingAddress = $this->order->getBillingAddress();

        return $this->taxCalculation->getRateRequest(
            $shippingAddress,
            $billingAddress,
            $customerTaxClassId,
            $store
        );
    }

    /**
     * @return void
     */
    public function reloadShippingRates()
    {
        $this->getOrder()->syncQuote();
    }

    /**
     * @return null
     */
    protected function getCurrentShippingRate()
    {
        if (empty($this->rate)) {
            $orderShippingCode = $this->getOrder()->getShippingMethod();

            $this->reloadShippingRates();

            $this->rate = $this->quote->load($this->getOrder()
                ->getQuoteId())
                ->getShippingAddress()
                ->getShippingRateByCode($orderShippingCode);

            $this->rate = $this->getShippingRateByCode($orderShippingCode);
        }

        return $this->rate;
    }

    /**
     * NOTE: We can not use
     * app/code/Magento/Quote/Model/Quote/Address::getShippingRateByCode($code)
     * because it using old rates
     *
     * @param string $orderShippingCode
     * @return bool
     */
    protected function getShippingRateByCode($orderShippingCode)
    {
        $address = $this->getQuote()->getShippingAddress();
        $id = $address->getId();
        $rates = $this->_rateCollectionFactory->create()->setAddressFilter($id);
        foreach ($rates as $rate) {
            if ($rate->getCode() == $orderShippingCode) {
                $rate->setAddress($address);
                return $rate;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isNotAvailable()
    {
        return $this->getCurrentShippingRate() == null;
    }

    /**
     * @return bool
     */
    public function isTotalChanged()
    {
        if (!$this->isNotAvailable()) {
            $currentShippingRate = $this->getCurrentShippingRate()->getPrice();
            $shippingAmount = $this->getOrder()->getShippingAmount();

            return $currentShippingRate != $shippingAmount;
        }

        return false;
    }
}
