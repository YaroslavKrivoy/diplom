<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_websites = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote
     */
    protected $quoteResourceModel;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteGiftCardCollectionFactory;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Amasty\GiftCard\Model\ResourceModel\Quote $quoteResourceModel
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $quoteGiftCardCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Amasty\GiftCard\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory $quoteGiftCardCollectionFactory
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->cartFactory = $cartFactory;
        $this->localeCurrency = $localeCurrency;
        $this->quoteResourceModel = $quoteResourceModel;
        $this->quoteGiftCardCollectionFactory = $quoteGiftCardCollectionFactory;
        $this->currencyFactory = $currencyFactory;
    }

    public function getWebsitesOptions()
    {
        if ($this->_websites === null) {
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->_websites[$website->getId()] = $website->getName();
            }
        }
        return $this->_websites;
    }

    public function formatPrice($price)
    {
        return $this->priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->storeManager->getStore()
        );
    }

    public function formatBasePrice($price)
    {
        return $this->priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->storeManager->getStore(),
            $this->storeManager->getStore()->getBaseCurrencyCode()
        );
    }

    public function convertAndFormatPrice($price)
    {
        return $this->priceCurrency->convertAndFormat(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->storeManager->getStore()
        );
    }

    public function convertPrice($price)
    {
        return $this->priceCurrency->convert(
            $price,
            $this->storeManager->getStore()
        );
    }

    public function getCardTypes()
    {
        return [
            \Amasty\GiftCard\Model\GiftCard::TYPE_COMBINED => [
                'value' => \Amasty\GiftCard\Model\GiftCard::TYPE_COMBINED,
                'label' => __('Both Virtual and Printed')
            ],
            \Amasty\GiftCard\Model\GiftCard::TYPE_PRINTED => [
                'value' => \Amasty\GiftCard\Model\GiftCard::TYPE_PRINTED,
                'label' => __('Only Printed')
            ],
            \Amasty\GiftCard\Model\GiftCard::TYPE_VIRTUAL => [
                'value' => \Amasty\GiftCard\Model\GiftCard::TYPE_VIRTUAL,
                'label' => __('Only Virtual')
            ],
        ];
    }

    public function getCardType($cardType)
    {
        $cardTypes = $this->getCardTypes();

        return isset($cardTypes[$cardType]) ? $cardTypes[$cardType]['label']
            : '';
    }

    public function getValueOrConfig($value, $xmlPath)
    {
        if ($value === null || $value === '') {
            $value = $this->scopeConfig->getValue(
                $xmlPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($xmlPath == 'amgiftcard/card/allow_message' && $value == 2) {
            $value = $this->scopeConfig->getValue(
                $xmlPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        return $value;
    }

    public function isEnableGiftFormInCart($quote = null)
    {
        if (!$this->isModuleActive()) {
            return false;
        }

        if ($quote === null) {
            $items = $this->cartFactory->create()->getItems();
        } else {
            $items = $quote->getAllItems();
        }
        $isAllowedGiftCard = true;
        $listAllowedProductTypes = $this->scopeConfig->getValue(
            'amgiftcard/general/allowed_product_types',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (empty($listAllowedProductTypes)) {
            return false;
        }
        $listAllowedProductTypes = explode(",", $listAllowedProductTypes);

        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $type = $item->getProduct()->getTypeId();
            // for grouped products
            foreach ($item->getOptions() as $option) {
                if ($option->getCode() == 'product_type') {
                    $type = $option->getValue();
                }
            }
            if (!in_array($type, $listAllowedProductTypes)) {
                $isAllowedGiftCard = false;
                break;
            }
        }

        return $isAllowedGiftCard;
    }

    public function removeAllCards($quote = null)
    {
        if ($quote === null) {
            $quote = $this->cartFactory->create();
        }
        $this->quoteResourceModel->removeAllCards($quote->getId());
    }

    public function isModuleActive($storeId = null)
    {
        $storeId = $this->storeManager->getStore($storeId)->getId();
        $isActive = $this->scopeConfig->getValue(
            'amgiftcard/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return (bool) $isActive;
    }

    public function getAmGiftCardFields()
    {
        $store = $this->storeManager->getStore();
        $_currencyShortName = $this->localeCurrency->getCurrency($store->getCurrentCurrencyCode())->getShortName();

        return [
            'am_giftcard_amount' => ['fieldName' => __('Card Value in %1', $_currencyShortName)],
            'am_giftcard_amount_custom' => ['fieldName' => __('Custom Card Value')],
            'am_giftcard_image' => ['fieldName' => __('Card Image')],
            'am_giftcard_type' => ['fieldName' => __('Card Type')],
            'am_giftcard_sender_name' => ['fieldName' => __('Sender Name')],
            'am_giftcard_sender_email' => ['fieldName' => __('Sender Email')],
            'am_giftcard_recipient_name' => ['fieldName' => __('Recipient Name')],
            'am_giftcard_recipient_email' => ['fieldName' => __('Recipient Email')],
            'am_giftcard_date_delivery' => ['fieldName' => __('Date of Certificate Delivery')],
            'am_giftcard_date_delivery_timezone' => ['fieldName' => __('Timezone')],
            'am_giftcard_message' => ['fieldName' => __('Message'), 'isCheck'=>false],
        ];
    }

    /**
     * @param $amount
     *
     * @return float
     */
    public function round($amount)
    {
        return $this->priceCurrency->round($amount);
    }

    /**
     * method to convert price from one currency to other
     * @param $amount
     * @param null $fromCurrency
     * @param null $toCurrency
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function currencyConvert(
        $amount,
        $fromCurrency = null,
        $toCurrency = null
    ) {
        $fromCurrency = $fromCurrency ? $fromCurrency : $this->storeManager->getStore()->getBaseCurrency();
        $toCurrency   = $toCurrency ? $toCurrency : $this->storeManager->getStore()->getCurrentCurrency();
        if (is_string($fromCurrency)) {
            $rateToBase = $this->currencyFactory->create()->load($fromCurrency)
                ->getAnyRate($this->storeManager->getStore()->getBaseCurrency()->getCode());
        } elseif ($fromCurrency instanceof \Magento\Directory\Model\Currency) {
            $rateToBase = $fromCurrency->getAnyRate($this->storeManager->getStore()->getBaseCurrency()->getCode());
        }
        $rateFromBase = $this->storeManager->getStore()->getBaseCurrency()->getRate($toCurrency);
        if ($rateToBase && $rateFromBase) {
            $amount = $amount * $rateToBase * $rateFromBase;
        } else {
            throw new LocalizedException(__('Please correct the target currency.'));
        }

        return $amount;
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function convertToBase($amount)
    {
        $store = $this->storeManager->getStore();
        $baseCurrency = $store->getBaseCurrency();
        $fromCurrency = $store->getCurrentCurrency();
        $currencyConvert = $this->currencyConvert($amount, $fromCurrency, $baseCurrency);

        return $currencyConvert;
    }

    /**
     * @return bool
     */
    public function isAllowedToPaidForShipping()
    {
        return $this->scopeConfig->isSetFlag(
            'amgiftcard/general/allow_to_paid_for_shipping',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isAllowedToPaidForTax()
    {
        return $this->scopeConfig->isSetFlag(
            'amgiftcard/general/allow_to_paid_for_tax',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $product
     *
     * @return mixed
     */
    public function getFirstOfAmount($product)
    {
        $productModel = $product->getPriceModel();
        $listOfAmounts = $productModel->getAmounts($product);
        if ($listOfAmounts) {
            return $listOfAmounts[0]["price"];
        }

        return null;
    }

    /**
     * @param $customAmount
     * @param $amount
     * @return mixed
     */
    public function getGiftAmount($customAmount, $amount)
    {
        if ($customAmount != "") {
            $value = $customAmount;
        } else {
            $value = $amount;
        }

        return $value;
    }
}
