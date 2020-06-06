<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

use MageWorx\OrderEditor\Model\Order\Item;

class Order extends \Magento\Sales\Model\Order
{
    /**
     * @var Item
     */
    protected $item;

    /**
     * @var []
     */
    protected $newParams = [];

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magento\Tax\Model\Config $taxConfig
     */
    protected $taxConfig = null;

    /**
     * @var \MageWorx\OrderEditor\Model\Order\Sales
     */
    protected $sales;

    /**
     * @var float
     */
    protected $oldTotal;
    
    /**
     * @var float
     */
    protected $oldQtyOrdered;

    /**
     * @var []
     */
    protected $addedItems = [];

    /**
     * @var []
     */
    protected $removedItems = [];
    
    /**
     * @var []
     */
    protected $increasedItems = [];
    
    /**
     * @var []
     */
    protected $decreasedItems = [];

    /**
     * @var []
     */
    protected $changesInAmounts = [];

    /**
     * @var \MageWorx\OrderEditor\Model\Quote
     */
    protected $quote;

    /**
     * @var \MageWorx\OrderEditor\Model\Invoice
     */
    protected $invoice;

    /**
     * @var \MageWorx\OrderEditor\Model\Creditmemo
     */
    protected $creditmemo;

    /**
     * @var \MageWorx\OrderEditor\Model\Shipment
     */
    protected $shipment;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory
     */
    protected $orderGridCollectionFactory;

    /**
     * @var \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    protected $taxCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    protected $orderHistoryCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param Item $item
     * @param \MageWorx\OrderEditor\Model\Quote $quote
     * @param \MageWorx\OrderEditor\Model\Order\Sales $sales
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \MageWorx\OrderEditor\Model\Invoice $invoice
     * @param \MageWorx\OrderEditor\Model\Shipment $shipment
     * @param \MageWorx\OrderEditor\Model\Creditmemo $creditmemo
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $taxCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory $orderGridCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory $paymentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $memoCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory $trackCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \MageWorx\OrderEditor\Model\Order\Item $item,
        \MageWorx\OrderEditor\Model\Quote $quote,
        \MageWorx\OrderEditor\Model\Order\Sales $sales,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \MageWorx\OrderEditor\Model\Invoice $invoice,
        \MageWorx\OrderEditor\Model\Shipment $shipment,
        \MageWorx\OrderEditor\Model\Creditmemo $creditmemo,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $orderHistoryCollectionFactory,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $taxCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory $orderGridCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->taxConfig = $taxConfig;
        $this->item = $item;
        $this->quote = $quote;
        $this->directoryHelper = $directoryHelper;
        $this->sales = $sales;
        $this->invoice = $invoice;
        $this->creditmemo = $creditmemo;
        $this->shipment = $shipment;
        $this->_scopeConfig = $scopeConfig;
        $this->orderGridCollectionFactory = $orderGridCollectionFactory;
        $this->taxCollectionFactory = $taxCollectionFactory;
        $this->orderHistoryCollectionFactory = $orderHistoryCollectionFactory;
        $this->customer = $customer;
        $this->quoteRepository = $quoteRepository;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $timezone,
            $storeManager,
            $orderConfig,
            $productRepository,
            $orderItemCollectionFactory,
            $productVisibility,
            $invoiceManagement,
            $currencyFactory,
            $eavConfig,
            $orderHistoryFactory,
            $addressCollectionFactory,
            $paymentCollectionFactory,
            $historyCollectionFactory,
            $invoiceCollectionFactory,
            $shipmentCollectionFactory,
            $memoCollectionFactory,
            $trackCollectionFactory,
            $salesOrderCollectionFactory,
            $priceCurrency,
            $productListFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return bool
     */
    public function hasItemsWithIncreasedQty()
    {
        return array_sum($this->increasedItems) > 0;
    }

    /**
     * @return bool
     */
    public function hasItemsWithDecreasedQty()
    {
        return array_sum($this->decreasedItems) > 0;
    }

    /**
     * @return bool
     */
    public function hasAddedItems()
    {
        return count($this->addedItems) > 0;
    }

    /**
     * @return bool
     */
    public function hasRemovedItems()
    {
        return count($this->removedItems) > 0;
    }

    /**
     * @return bool
     */
    public function hasChangesInAmounts()
    {
        return count($this->changesInAmounts) > 0;
    }

    /**
     * @return bool
     */
    public function isTotalWasChanged()
    {
        return $this->getChangesInTotal() != 0;
    }

    /**
     * @return float
     */
    public function getChangesInTotal()
    {
        return $this->oldTotal - $this->getCurrentOrderTotal();
    }

    /**
     * @return float
     */
    public function getChangesInTotalQty()
    {
        $currentQty = $this->getTotalQtyOrdered();
        return $this->oldQtyOrdered - $currentQty;
    }

    /**
     * @return float
     */
    protected function getCurrentOrderTotal()
    {
        return $this->getGrandTotal() - $this->getTotalRefunded();
    }

    /**
     * @return void
     */
    protected function beforeEditItems()
    {
        $this->oldTotal = $this->getCurrentOrderTotal();
        $this->oldQtyOrdered = $this->getTotalQtyOrdered();
        $this->addedItems = [];
        $this->removedItems = [];
        $this->increasedItems = [];
        $this->decreasedItems = [];
        $this->changesInAmounts = [];
    }

    /**
     * @param string[] $params
     * @return void
     * @throws \Exception
     */
    public function editItems($params)
    {
        $this->beforeEditItems();
        $this->prepareParamsForEditItems($params);
        //$this->checkStatus();
        $this->updateOrderItems();
        $this->collectOrderTotals();
        $this->_eventManager->dispatch('send_comment_order_change', ['oldItemsData' => $this->_registry->registry('oldItemsData'),
            'oldOrderData' => $this->getOrigData(), 'newItemsData' => $this->getItems(), 'newOrderData' => $this->getData()]);
    }

    /**
     * @return void
     */
    public function updatePayment()
    {
        $this->sales->setOrder($this)->updateSalesObjects();
    }

    /**
     * @param string[] $params
     * @return void
     * @throws \Exception
     */
    protected function prepareParamsForEditItems($params)
    {
        if (!isset($params['order_id']) || !isset($params['item'])) {
            throw new \Exception('Incorrect params for edit order items');
        }

        $this->load($params['order_id']);
        $this->newParams = $params['item'];
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function checkStatus()
    {
        if (!$this->isAllowEditOrder()) {
            throw new \Exception("You can't edit order with current status.");
        }
    }

    /**
     * @return void
     */
    protected function updateOrderItems()
    {
        $oldItemsData = [];
        $count = 0;
        foreach ($this->newParams as $id => $params) {
            $item = $this->loadOrderItem($id, $params);
            /* var $item \MageWorx\OrderEditor\Model\Order\Item */
            if ($count < $this->getTotalItemCount()) {
                $oldItemsData[$item->getId()] = $item->getOrigData();
                $oldItemsData[$item->getId()]['is_old'] = true;
            }
            $orderItem = $item->editItem($params, $this);

            $this->collectItemsChanges($orderItem);
            $this->editNewItem($orderItem->getItemId(), $params);
            $count++;
        }
        $this->_registry->register('oldItemsData', $oldItemsData);
    }

    /**
     * @param int $id
     * @param string[] $params
     * @return \MageWorx\OrderEditor\Model\Order\Item
     */
    protected function loadOrderItem($id, $params)
    {
        $item = clone $this->item;

        if (!isset($params['quote_item'])) {
            if (isset($params['action']) && $params['action'] == 'remove') {
                $this->removedItems[] = $id;
            }
            $item = $item->load($id);
        }

        return $item;
    }

    /**
     * @param Item $orderItem
     * @return void
     */
    protected function collectItemsChanges($orderItem)
    {
        $itemId = $orderItem->getItemId();
        $this->increasedItems[$itemId] = $orderItem->getIncreasedQty();
        $this->decreasedItems[$itemId] = $orderItem->getDecreasedQty();

        $changes = $orderItem->getChangesInAmounts();
        if (!empty($changes)) {
            $this->changesInAmounts[$itemId] = $changes;
        }
    }

    /**
     * @param int $id
     * @param string[] $params
     * @return void
     */
    protected function editNewItem($id, $params)
    {
        if (isset($params['item_type']) && $params['item_type'] == 'quote') {
            $this->addedItems[] = $id;

            unset($params['action']);
            unset($params['item_type']);

            $item = clone $this->item;
            $item = $item->load($id);
            $item->editItem($params, $this);
        }
    }

    /**
     * @return void
     */
    public function collectOrderTotals()
    {
        $totalQtyOrdered = 0;
        $weight = 0;
        $totalItemCount = 0;
        $baseDiscountTaxCompensationAmount = 0;
        $baseDiscountAmount = 0;
        $baseTotalWeeeDiscount = 0;
        $baseSubtotal = 0;
        $baseSubtotalInclTax = 0;

        /** @var $orderItem \MageWorx\OrderEditor\Model\Order\Item */
        foreach ($this->getItemsCollection() as $orderItem) {
            $baseDiscountAmount += $orderItem->getBaseDiscountAmount();

            //bundle part
            if ($orderItem->getParentItem()) {
                continue;
            }

            $baseDiscountTaxCompensationAmount += $orderItem->getBaseDiscountTaxCompensationAmount();

            $totalQtyOrdered += $orderItem->getQtyOrdered();
            $totalItemCount++;
            $weight += $orderItem->getRowWeight();
            $baseSubtotal += $orderItem->getBaseRowTotal(); /* RowTotal for item is a subtotal */
            $baseSubtotalInclTax += $orderItem->getBaseRowTotalInclTax();
            $baseTotalWeeeDiscount += $orderItem->getBaseDiscountAppliedForWeeeTax();
        }

        /* convert currency */
        $baseCurrencyCode = $this->getBaseCurrencyCode();
        $orderCurrencyCode = $this->getOrderCurrencyCode();

        if ($baseCurrencyCode === $orderCurrencyCode) {
            $discountAmount = $baseDiscountAmount + $this->getBaseShippingDiscountAmount();
            $discountTaxCompensationAmount = $baseDiscountTaxCompensationAmount;
            $subtotal = $baseSubtotal;
            $subtotalInclTax = $baseSubtotalInclTax;
        } else {
            $discountAmount = $this->directoryHelper
                ->currencyConvert($baseDiscountAmount + $this->getBaseShippingDiscountAmount(), $baseCurrencyCode, $orderCurrencyCode);
            $discountTaxCompensationAmount = $this->directoryHelper
                ->currencyConvert($baseDiscountTaxCompensationAmount, $baseCurrencyCode, $orderCurrencyCode);
            $subtotal = $this->directoryHelper
                ->currencyConvert($baseSubtotal, $baseCurrencyCode, $orderCurrencyCode);
            $subtotalInclTax = $this->directoryHelper
                ->currencyConvert($baseSubtotalInclTax, $baseCurrencyCode, $orderCurrencyCode);
        }

        $this->setTotalQtyOrdered($totalQtyOrdered)
            ->setWeight($weight)
            ->setSubtotal($subtotal)->setBaseSubtotal($baseSubtotal)
            ->setSubtotalInclTax($subtotalInclTax)
            ->setBaseSubtotalInclTax($baseSubtotalInclTax)
            ->setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
            ->setBaseDiscountTaxCompensationAmount($baseDiscountTaxCompensationAmount)
            ->setDiscountAmount('-' . $discountAmount)
            ->setBaseDiscountAmount('-' . $baseDiscountAmount)
            ->setTotalItemCount($totalItemCount);
            
        $this->save();
        
        $this->reCalculateTaxAmount();
        $this->calculateGrandTotal();
        $this->updateOrderTaxTable();
        
    }

    /**
     * @return void
     */
    public function calculateGrandTotal()
    {
        $this->reCalculateTaxAmount();

        // shipping tax
        $tax = $this->getTaxAmount() + $this->getShippingTaxAmount();
        $baseTax = $this->getBaseTaxAmount() + $this->getBaseShippingTaxAmount();

        $this->setTaxAmount($tax)->setBaseTaxAmount($baseTax)->save();

        // Order GrandTotal include tax
        if ($this->checkTaxConfiguration()) {
            $grandTotal = $this->getSubtotal()
                + $this->getTaxAmount()
                + $this->getShippingAmount()
                - abs($this->getDiscountAmount());
            $baseGrandTotal = $this->getBaseSubtotal()
                + $this->getBaseTaxAmount()
                + $this->getBaseShippingAmount()
                - abs($this->getBaseDiscountAmount());
        } else {
            $grandTotal = $this->getSubtotalInclTax()
                + $this->getShippingInclTax()
                - abs($this->getDiscountAmount());
            $baseGrandTotal = $this->getBaseSubtotalInclTax()
                + $this->getBaseShippingInclTax()
                - abs($this->getBaseDiscountAmount());
        }

        $this->setGrandTotal($grandTotal)
            ->setBaseGrandTotal($baseGrandTotal)
            ->save();

        $this->addCustomPriceToOrderGrandTotal();
    }

    /**
     * @return void
     */
    protected function reCalculateTaxAmount()
    {
        $baseTaxAmount = 0;

        /**
         * @var $orderItem \MageWorx\OrderEditor\Model\Order\Item
         */
        foreach ($this->getItemsCollection() as $orderItem) {
            if ($orderItem->getParentItem()) {
                continue;
            }
            $baseTaxAmount += $orderItem->getBaseTaxAmount();
        }

        $baseCurrencyCode = $this->getBaseCurrencyCode();
        $orderCurrencyCode = $this->getOrderCurrencyCode();
        if ($baseCurrencyCode === $orderCurrencyCode) {
            $taxAmount = $baseTaxAmount;
        } else {
            $taxAmount = $this->directoryHelper->currencyConvert(
                $baseTaxAmount,
                $baseCurrencyCode,
                $orderCurrencyCode
            );
        }

        $this->setTaxAmount($taxAmount)->setBaseTaxAmount($baseTaxAmount);
        $this->save();
    }

    /**
     * @return bool
     */
    public function checkTaxConfiguration()
    {
        $catalogPrices = $this->taxConfig->priceIncludesTax() ? 1 : 0;
        $shippingPrices = $this->taxConfig->shippingPriceIncludesTax() ? 1 : 0;
        $applyTaxAfterDiscount = $this->taxConfig->applyTaxAfterDiscount() ? 1 : 0;

        return !$catalogPrices && !$shippingPrices && $applyTaxAfterDiscount;
    }

    /**
     * @return void
     */
    public function updateOrderTaxTable()
    {
    }

    /**
     * @return $this
     */
    public function syncQuote()
    {
        $this->syncQuoteItems();
        $this->syncAddresses();
        $this->collectQuoteTotals();

        return $this;
    }

    /**
     * @return void
     */
    protected function collectQuoteTotals()
    {
        if (!$this->getIsVirtual()) {
            $this->quote->getShippingAddress()
                ->setShippingMethod($this->getShippingMethod())
                ->setCollectShippingRates(true);
        }

        $this->quote->setTotalsCollectedFlag(false);
        $this->quote->collectTotals();

        $this->quoteRepository->save($this->quote);
    }

    /**
     * @return void
     */
    protected function syncQuoteItems()
    {
        try {
            $orderItems = [];
            foreach ($this->getItems() as $item) {
                $orderItems[$item->getQuoteItemId()] = $item;
            }
            $quoteItemIds = array_keys($orderItems);

            $quoteId = $this->getQuoteId();
            $quoteItems = $this->quote->loadByIdWithoutStore($quoteId)->getAllItems();

            foreach ($quoteItems as $quoteItem) {
                /**
                 * @var \Magento\Quote\Model\Quote\Item $quoteItem
                 */
                $quoteItemId = $quoteItem->getItemId();

                if (!in_array($quoteItemId, $quoteItemIds)) {
                    $quoteItem->delete();
                } else {
                    $orderItem = $orderItems[$quoteItemId];
                    $qty = $orderItem->getQtyOrdered()
                        - $orderItem->getQtyRefunded()
                        - $orderItem->getQtyCanceled();

                    $buyRequest = new \Magento\Framework\DataObject(
                        [
                            'qty' => $qty,
                            'custom_price' => $orderItem->getPriceInclTax()
                        ]
                    );
                    $this->quote->updateItem($quoteItem, $buyRequest)->save();
                }
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function syncAddresses()
    {
        if (!$this->getIsVirtual()) {
            $data = $this->getShippingAddress()->getData();
            $quoteAddress = $this->quote->getShippingAddress();
            $data['address_id'] = $quoteAddress->getAddressId();
            $quoteAddress->setData($data);
            $quoteAddress->save();
        }

        $data = $this->getBillingAddress()->getData();
        $quoteAddress = $this->quote->getBillingAddress();
        $data['address_id'] = $quoteAddress->getAddressId();
        $quoteAddress->setData($data);
        $quoteAddress->save();
    }

    /**
     * @return void
     */
    protected function addCustomPriceToOrderGrandTotal()
    {
    }

    /**
     * @return bool
     */
    public function isAllowEditOrder()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAllowDeleteOrder()
    {
        return true;
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $this->deleteRelatedShipments();
        $this->deleteRelatedInvoices();
        $this->deleteRelatedCreditMemos();
        $this->deleteRelatedOrderInfo();

        return parent::beforeDelete();
    }

    /**
     * @return void
     */
    protected function deleteRelatedOrderInfo()
    {
        try {
            $collection = $this->_addressCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->_orderItemCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->_paymentCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->orderHistoryCollectionFactory->create()->setOrderFilter($this);
            foreach ($collection as $object) {
                $object->delete();
            }

            $collection = $this->taxCollectionFactory->create()->loadByOrder($this);
            foreach ($collection as $object) {
                $object->delete();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function deleteRelatedInvoices()
    {
        try {
            $collection = $this->getInvoiceCollection();
            foreach ($collection as $item) {
                $object = $this->invoice->load($item->getId());
                $object->delete();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function deleteRelatedShipments()
    {
        try {
            $collection = $this->getShipmentsCollection();
            foreach ($collection as $item) {
                $object = $this->shipment->load($item->getId());
                $object->delete();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function deleteRelatedCreditMemos()
    {
        try {
            $collection = $this->getCreditmemosCollection();
            foreach ($collection as $item) {
                $object = $this->creditmemo->load($item->getId());
                $object->delete();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param string $status
     * @return void
     */
    public function updateOrderStatus($status)
    {
        $oldStatus = $this->getStatus();
        $newStatus = $status;

        $this->setData('status', $status)->save();

        if ($oldStatus != $newStatus) {
        }
    }

    /**
     * @return $this
     */
    public function getCustomer()
    {
        return $this->customer->load($this->getCustomerId());
    }
}
