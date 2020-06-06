<?php

namespace KozakGroup\RewriteOrderEditor\Model\Order;

use MageWorx\OrderEditor\Helper\Data as Helper;

class Item extends \MageWorx\OrderEditor\Model\Order\Item
{

    /**
     * @var array
     */
    protected $newParams = [];

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Directory\Helper\Data $directoryHelper,
        Helper $helper,
        \Magento\CatalogInventory\Api\StockManagementInterface $stockManagement,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory $linkPurchasedFactory,
        \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory $linkPurchasedItemsFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Downloadable\Model\Link $downloadableLink,
        \MageWorx\OrderEditor\Model\Quote\Item $quoteItem,
        \MageWorx\OrderEditor\Model\Invoice $invoice,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $orderFactory,
            $storeManager,
            $productRepository,
            $directoryHelper,
            $helper,
            $stockManagement,
            $stockRegistryProvider,
            $stockConfiguration,
            $scopeConfig,
            $purchasedFactory,
            $objectManager,
            $linkPurchasedFactory,
            $linkPurchasedItemsFactory,
            $objectCopyService,
            $downloadableLink,
            $quoteItem,
            $invoice,
            $resource,
            $resourceCollection,
            $data);
        $this->directoryHelper           = $directoryHelper;
        $this->helper                    = $helper;
    }

    /**
     * Update Item Data
     *
     * @return void
     */
    protected function updateItemData()
    {
        // description
        if (isset($this->newParams['description'])) {
            $this->setDescription($this->newParams['description']);
        }

        // tax amount
        if (isset($this->newParams['tax_amount'])) {
            $taxAmount = $this->newParams['tax_amount'];
            $baseTaxAmount = $this->currencyConvertToBaseCurrency($taxAmount);

            $this->setBaseTaxAmount($baseTaxAmount)
                ->setTaxAmount($taxAmount)
                ->setBaseTaxInvoiced(0)
                ->setTaxInvoiced(0);
        }

        // discount tax compensation amount
        if (isset($this->newParams['discount_tax_compensation_amount'])) {
            $hiddenTax = $this->newParams['discount_tax_compensation_amount'];
            $baseHiddenTax = $this->currencyConvertToBaseCurrency($hiddenTax);

            $this->setBaseDiscountTaxCompensationAmount($baseHiddenTax)
                ->setDiscountTaxCompensationAmount($hiddenTax);
        }

        // tax percent
        if (isset($this->newParams['tax_percent'])) {
            $this->setTaxPercent($this->newParams['tax_percent']);
        }

        // case_qty
        if (isset($this->newParams['fact_cases'])) {
            $this->setCaseQty($this->newParams['fact_cases']);
        }

        // price
        if (isset($this->newParams['price'])) {
            $price = $this->newParams['price'];
            $basePrice = $this->currencyConvertToBaseCurrency($price);

            $this->setBasePrice($basePrice)
                ->setPrice($price);
        }

        // Price includes tax
        if (isset($this->newParams['price_incl_tax'])) {
            $priceInclTax = $this->newParams['price_incl_tax'];
            $basePriceInclTax = $this->currencyConvertToBaseCurrency($priceInclTax);

            $this->setBasePriceInclTax($basePriceInclTax)
                ->setPriceInclTax($priceInclTax);
        }

        // discount amount
        if (isset($this->newParams['discount_amount'])) {
            $discountAmount = $this->newParams['discount_amount'];
            $baseDiscountAmount = $this->currencyConvertToBaseCurrency($discountAmount);

            $this->setBaseDiscountAmount($baseDiscountAmount)
                ->setDiscountAmount($discountAmount)
                ->setBaseDiscountInvoiced(0)
                ->setDiscountInvoiced(0);
        }

        // discount percent
        if (isset($this->newParams['discount_percent'])) {
            $this->setDiscountPercent($this->newParams['discount_percent']);
        }

        // subtotal (row total)
        if (isset($this->newParams['subtotal'])) {
            $currentSubtotal = $this->newParams['subtotal'];
            $baseCurrencySubtotal = $this->currencyConvertToBaseCurrency($currentSubtotal);
            $roundBaseCurrencySubtotal = $this->helper->roundAndFormatPrice($baseCurrencySubtotal);

            $this->setBaseRowTotal($roundBaseCurrencySubtotal)
                ->setRowTotal($currentSubtotal)
                ->setBaseRowInvoiced(0)
                ->setRowInvoiced(0);
        }

        // Subtotal includes tax
        if (isset($this->newParams['subtotal_incl_tax'])) {
            $subtotalInclTax = $this->newParams['subtotal_incl_tax'];
            $baseCurrencySubtotalInclTax = $this->currencyConvertToBaseCurrency($subtotalInclTax);
            $roundBaseCurrencySubtotalInclTax = $this->helper->roundAndFormatPrice($baseCurrencySubtotalInclTax);

            $this->setBaseRowTotalInclTax($roundBaseCurrencySubtotalInclTax)
                ->setRowTotalInclTax($subtotalInclTax);
        }

        $this->save();
    }

    /**
     * @param float $basePrice
     * @return float
     */
    protected function currencyConvertToBaseCurrency($basePrice)
    {
        $baseCurrency  = $this->getOrder()->getBaseCurrencyCode();
        $orderCurrency = $this->getOrder()->getOrderCurrencyCode();
        if ($baseCurrency === $orderCurrency) {
            return $basePrice;
        }

        return $this->directoryHelper
            ->currencyConvert($basePrice, $orderCurrency, $baseCurrency);
    }

}
