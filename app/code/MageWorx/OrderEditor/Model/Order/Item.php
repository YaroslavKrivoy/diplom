<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Order;

use \Magento\Framework\DataObject;
use MageWorx\OrderEditor\Helper\Data as Helper;

class Item extends \Magento\Sales\Model\Order\Item
{
    /**
     * @var array
     */
    protected $newParams = [];

    /**
     * @var array
     */
    protected $changes = [];

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var float
     */
    protected $deltaForComparesOrders = 0.02;

    /**
     * @var mixed
     */
    protected $oldData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Downloadable\Model\Link\PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory
     */
    protected $linkPurchasedItemsFactory;

    /**
     * @var \Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory
     */
    protected $linkPurchasedFactory;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $_objectCopyService;

    /**
     * @var \Magento\Downloadable\Model\Link
     */
    protected $downloadableLink;

    /**
     * @var \MageWorx\OrderEditor\Model\Quote\Item
     */
    protected $quoteItem;

    /**
     * @var float
     */
    protected $increasedQty;

    /**
     * @var float
     */
    protected $decreasedQty;

    /**
     * @var \MageWorx\OrderEditor\Model\Invoice
     */
    protected $invoice;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param Helper $helper
     * @param \Magento\CatalogInventory\Api\StockManagementInterface $stockManagement
     * @param \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Downloadable\Model\ResourceModel\Link\Purchased\CollectionFactory $linkPurchasedFactory
     * @param \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory $linkPurchasedItemsFactory
     * @param DataObject\Copy $objectCopyService
     * @param \Magento\Downloadable\Model\Link $downloadableLink
     * @param \MageWorx\OrderEditor\Model\Quote\Item $quoteItem
     * @param \MageWorx\OrderEditor\Model\Invoice $invoice
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
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
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $orderFactory,
            $storeManager,
            $productRepository,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_scopeConfig              = $scopeConfig;
        $this->_purchasedFactory         = $purchasedFactory;
        $this->directoryHelper           = $directoryHelper;
        $this->helper                    = $helper;
        $this->stockManagement           = $stockManagement;
        $this->stockRegistryProvider     = $stockRegistryProvider;
        $this->stockConfiguration        = $stockConfiguration;
        $this->objectManager             = $objectManager;
        $this->linkPurchasedFactory      = $linkPurchasedFactory;
        $this->linkPurchasedItemsFactory = $linkPurchasedItemsFactory;
        $this->_objectCopyService        = $objectCopyService;
        $this->downloadableLink          = $downloadableLink;
        $this->quoteItem                 = $quoteItem;
        $this->invoice                   = $invoice;
    }

    /**
     * @param string[] $params
     * @param \Magento\Sales\Model\Order $order
     * @return $this|Item
     * @throws \Exception
     */
    public function editItem($params, $order)
    {
        $this->initParams($params, $order);

        if (isset($this->newParams['fact_qty'])
            && $this->newParams['fact_qty'] <= 0
        ) {
            return $this;
        }

        // remove item
        if ($this->isRemovedItem()) {
            $this->removeOrderItem();

            return $this;
        }

        // add new item
        if ($this->isNewItem()) {
            return $this->addNewOrderItem();
        }

        // edit item
        $this->editOrderItem();

        return $this;
    }

    /**
     * @param string[] $params
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function initParams($params, $order)
    {
        $this->newParams = $params;
        $this->setOrder($order);
    }

    /**
     * @return bool
     */
    protected function isRemovedItem()
    {
        return isset($this->newParams['action'])
            && $this->newParams['action'] == 'remove';
    }

    /**
     * @return bool
     */
    public function isNewItem()
    {
        return isset($this->newParams['item_type'])
            && $this->newParams['item_type'] == 'quote';
    }

    /**
     * @return void
     */
    protected function removeOrderItem()
    {
        try {
            $this->cancelInvoices();

            $this->removeDownloadablePurchasedLinks();
            $this->removeRelatedOrderItems();

            $this->reduceProductInStock(0, $this);
            $this->removeQuoteItems($this);
            $this->delete();
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function removeRelatedOrderItems()
    {
        if ($this->getProductType() == 'configurable') {
            $simpleOrderItems = $this->getCollection()
                                     ->addFieldToFilter('parent_item_id', $this->getItemId());

            /**
             * @var $simpleOrderItem \MageWorx\OrderEditor\Model\Order\Item
             */
            foreach ($simpleOrderItems as $simpleOrderItem) {
                $this->reduceProductInStock(0, $simpleOrderItem);
                $this->removeQuoteItems($simpleOrderItem);
                $simpleOrderItem->delete();
            }
        }
    }

    /**
     * @return void
     */
    protected function cancelInvoices()
    {
        $invoices = $this->invoice->getCollection()
                                  ->addFieldToFilter('order_id', $this->getOrderId());

        /** @var $invoice \MageWorx\OrderEditor\Model\Invoice */
        foreach ($invoices as $invoice) {
            $invoice->cancel();
            $this->objectManager->create('Magento\Framework\DB\Transaction')
                                ->addObject($invoice->getOrder())
                                ->save();
        }
    }


    /**
     * @var $orderItem \MageWorx\OrderEditor\Model\Order\Item
     * @return void
     */
    protected function removeQuoteItems($orderItem)
    {
        try {
            $quoteItemId = $orderItem->getQuoteItemId();
            $this->quoteItem->load($quoteItemId)->delete();
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function removeDownloadablePurchasedLinks()
    {
        if ($this->getProductType() == 'downloadable') {
            $purchased = $this->_purchasedFactory->create()->load(
                $this->getId(),
                'order_item_id'
            );
            $purchased->delete();
        }
    }

    /**
     * @return Item
     * @throws \Exception
     */
    protected function addNewOrderItem()
    {
        if (!isset($this->newParams['item_id'])) {
            throw new \Exception('Item id is not defined');
        }

        $quoteItemId = $this->newParams['item_id'];

        /** @var $converter \MageWorx\OrderEditor\Model\Edit\Quote */
        $converter = $this->objectManager->create('MageWorx\OrderEditor\Model\Edit\Quote');

        /** @var $orderItem \MageWorx\OrderEditor\Model\Order\Item */
        $orderItem = $converter->getUpdatedOrderItem($quoteItemId);

        $orderItem->setData('order_id', $this->getOrder()->getId());
        $orderItem->setData('store_id', $this->getOrder()->getStoreId());
        $qty = array_key_exists("fact_qty", $this->newParams) ? $this->newParams['fact_qty'] : 0;
        $orderItem->setData('qty_ordered', $qty);

        $orderItem->save();

        return $orderItem;
    }

    /**
     * @return void
     */
    protected function editOrderItem()
    {
        $this->saveOldData();
        $this->updateProductOptions();
        $this->updateQty();
        $this->updateItemData();
        $this->updateOrderTaxItemTable();

        $this->detectChangesInAmounts();
    }

    /**
     * @return string
     */
    protected function getItemLevel()
    {
        return 'order_item_' . $this->getId();
    }

    /**
     * @return void
     */
    protected function saveOldData()
    {
        $this->oldData = $this->getData();
    }

    /**
     * @return void
     */
    protected function detectChangesInAmounts()
    {
        $map = [
            'row_total',
            'base_row_total',
            'tax_refunded',
            'base_tax_amount',
            'discount_amount',
            'base_discount_amount'
        ];

        $oldRowTotal = $this->getOrderItemRowTotal($this->oldData);
        $newRowTotal = $this->getOrderItemRowTotal($this->getData());

        if (abs($oldRowTotal - $newRowTotal) >= $this->deltaForComparesOrders) {
            foreach ($map as $i) {
                if (isset($this->oldData[$i])) {
                    $this->changes[$i] = $this->oldData[$i] - $this->getData($i);
                }
            }
        }
    }

    /**
     * @return string[]
     */
    public function getChangesInAmounts()
    {
        return $this->changes;
    }

    /**
     * @return void
     */
    protected function updateProductOptions()
    {
        if (!isset($this->newParams['product_options']) || empty($this->newParams['product_options'])) {
            return;
        }

        $this->editDownloadItem();

        // options
        $productOptions = $this->newParams['product_options'];
        $this->setData('product_options', $productOptions);

        $oldSimpleSku = $this->getSku();
        $newSimpleSku = $this->updateSkuAfterUpdateOptions();

        $this->updateInventoryAfterUpdateOptions($oldSimpleSku, $newSimpleSku);

        $this->save();
    }

    /**
     * @return string
     */
    protected function updateSkuAfterUpdateOptions()
    {
        $productOptions = $this->getData('product_options');
        $options        = $this->helper->decodeBuyRequestValue($productOptions);

        if (isset($options['simple_sku']) && !empty($options['simple_sku'])) {
            $this->setSku($options['simple_sku']);
        } else {
            if (isset($this->newParams['sku']) && !empty($this->newParams['sku'])) {
                $this->setSku($this->newParams['sku']);
            }
        }

        if (isset($options['simple_name']) && !empty($options['simple_name'])) {
            $this->setName($options['simple_name']);
        }

        return $this->getSku();
    }

    /**
     * @param string $oldSimpleSku
     * @param string $newSimpleSku
     * @return void
     */
    protected function updateInventoryAfterUpdateOptions($oldSimpleSku, $newSimpleSku)
    {
        if ($oldSimpleSku == $newSimpleSku) {
            return;
        }

        // update product id for simple product
        try {
            $oldProductId = $this->productRepository->get($oldSimpleSku)->getId();
            $newProductId = $this->productRepository->get($newSimpleSku)->getId();

            if ($this->getProductType() == 'configurable') {
                $simpleOrderItem = $this->getCollection()->addFieldToFilter('parent_item_id', $this->getItemId());
                if ($simpleOrderItem->getSize() > 0) {
                    $simpleOrderItem->getFirstItem()
                                    ->setProductId($newProductId)
                                    ->setSku($newSimpleSku)
                                    ->setName($this->getName())
                                    ->setQtyOrdered(0)
                                    ->save();
                }
            } elseif ($this->getProductType() == 'simple') {
                $this->setProductId($newProductId);
            }

            // prepare qty
            $qty = $this->getQtyOrdered() - $this->getQtyRefunded() - $this->getQtyCanceled();
            $qty = $qty < 0 ? 0 : $qty;

            // back to inventory OLD item
            $this->productToInventory($qty, $oldProductId);

            // get from inventory NEW item
            $this->setQtyOrdered(0);
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function editDownloadItem()
    {
        if ($this->getProductType() != 'downloadable') {
            return;
        }

        $newLinks = $this->getOptionLinks($this->newParams['product_options']);
        $oldLinks = $this->getOptionLinks($this->getData('product_options'));

        $added = array_diff($newLinks, $oldLinks);
        foreach ($added as $linkId) {
            $this->addDownloadableLink($linkId);
        }

        $removed = array_diff($oldLinks, $newLinks);
        foreach ($removed as $linkId) {
            $this->removeDownloadableLink($linkId);
        }
    }

    /**
     * @param  string[]|string $productOptions
     * @return string[]
     */
    protected function getOptionLinks($productOptions)
    {
        $productOptions = is_string($productOptions)
            ? $this->helper->decodeBuyRequestValue($productOptions)
            : $productOptions;

        return isset($productOptions["links"])
            ? $productOptions["links"]
            : [];
    }

    /**
     * @param int $linkId
     * @return void
     */
    protected function addDownloadableLink($linkId)
    {
        $linkPurchasedItem = $this->objectManager->get('Magento\Downloadable\Model\Link\Purchased\Item');

        $linkPurchasedId = $this->getLinkPurchasedIdForOrderItem();

        $this->_objectCopyService->copyFieldsetToTarget(
            'downloadable_sales_copy_link',
            'to_purchased',
            $linkId,
            $linkPurchasedItem
        );

        $hash     = microtime() . $linkPurchasedId . $this->getId() . $this->getProductId();
        $linkHash = strtr(base64_encode($hash), '+/=', '-_,');

        /**
         * @var $link \Magento\Downloadable\Model\Link
         */
        $link = $this->downloadableLink->getCollection()
                                       ->addTitleToResult()
                                       ->addFieldToFilter('main_table.link_id', $linkId)
                                       ->getFirstItem();

        $numberOfDownloads = $link->getNumberOfDownloads() * $this->getQtyOrdered();

        $linkPurchasedItem
            ->setPurchasedId($linkPurchasedId)
            ->setOrderItemId($this->getId())
            ->setLinkHash($linkHash)
            ->setNumberOfDownloadsBought($numberOfDownloads)
            ->setStatus(\Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING)
            ->setCreatedAt($this->getCreatedAt())
            ->setUpdatedAt($this->getUpdatedAt())
            ->setProductId($this->getProductId())
            ->setLinkId($link->getId())
            ->setIsShareable($link->getIsShareable())
            ->setLinkUrl($link->getLinkUrl())
            ->setLinkFile($link->getLinkFile())
            ->setLinkType($link->getLinkType())
            ->setLinkTitle($link->getDefaultTitle())
            ->save();
    }

    /**
     * @return int
     */
    protected function getLinkPurchasedIdForOrderItem()
    {
        $collection = $this->linkPurchasedFactory->create()
                                                 ->addFieldToFilter('order_item_id', $this->getId());

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem()->getId();
        }

        return 0;
    }

    /**
     * @param int $linkId
     * @return void
     */
    protected function removeDownloadableLink($linkId)
    {
        try {
            $purchasedItems = $this->linkPurchasedItemsFactory->create()
                                                              ->addFieldToFilter('order_item_id', $this->getId())
                                                              ->addFieldToFilter('link_id', $linkId);

            foreach ($purchasedItems as $link) {
                $link->delete();
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    protected function updateOrderTaxItemTable()
    {
    }

    /**
     * @return void
     */
    protected function updateQty()
    {
        // qty ordered
        $oldQtyOrdered = $this->getQtyOrdered() - $this->getQtyRefunded();
        $newQty        = isset($this->newParams['fact_qty'])
            ? $this->newParams['fact_qty']
            : $oldQtyOrdered;

        if ($this->getProductType() == 'configurable') {
            $collection = $this->getCollection()
                               ->addFieldToFilter('parent_item_id', $this->getItemId());
            if ($collection->getSize() > 0) {
                /** @var Item $simpleOrderItem */
                $simpleOrderItem = $collection->getFirstItem();
                $this->updateQtyProduct($newQty, $simpleOrderItem);
            }
        }
        $this->updateQtyProduct($newQty, $this);
    }

    /**
     * @param float $newQty
     * @param Item $item
     * @return float
     */
    protected function updateQtyProduct($newQty, $item)
    {
        if ($item->getQtyOrdered() > $newQty) {
            /* product was removed */
            $this->decreasedQty = $this->reduceProductInStock($newQty, $item);
        } else {
            /* product was added */
            $this->increasedQty = $this->increaseProductInStock($newQty, $item);
        }

        $item->setQtyOrdered($newQty);
        $item->setRowWeight(
            $item->getWeight() * $newQty - $item->getQtyRefunded()
        );
        $item->save();

        return $newQty;
    }

    /**
     * @param int $newQty
     * @param array $item
     * @return float
     */
    protected function reduceProductInStock($newQty, $item)
    {
        $qty = $item->getQtyOrdered() - $newQty - $item->getQtyRefunded();

        if ($qty > 0 && $this->getAllowReturnToStock()) {
            $this->productToInventory($qty, $item->getProductId());
        }

        return $qty;
    }

    /**
     * @param float $qty
     * @param int $productId
     */
    protected function productToInventory($qty, $productId)
    {
        $this->stockManagement->backItemQty($productId, $qty, $this->getStore()->getWebsiteId());
    }

    /**
     * @return bool
     */
    protected function getAllowReturnToStock()
    {
        if (isset($this->newParams['back_to_stock'])) {
            return $this->newParams['back_to_stock'] ? true : false;
        }

        return false;
    }

    /**
     * Increase Product In Stock
     *
     * @param float $newQty
     * @param Item $item
     * @return float
     */
    protected function increaseProductInStock($newQty, $item)
    {
        $qty = $newQty - ($item->getQtyOrdered());
        $qty = $qty < 0 ? 0 : $qty;

        if ($qty != 0) {
            $this->productFromInventory($qty, $item->getProductId());
        }

        return $qty;
    }


    /**
     * Remove Product From Inventory
     *
     * @param float|int $qty
     * @param int $productId
     * @return bool
     */
    protected function productFromInventory($qty, $productId)
    {
        $websiteId = $this->getStore()->getWebsiteId();
        $stockItem = $this->stockRegistryProvider
            ->getStockItem($productId, $websiteId);

        $stockItem->setQty($stockItem->getQty() - $qty);
        $stockItem->save();

        return true;
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
            $taxAmount     = $this->newParams['tax_amount'];
            $baseTaxAmount = $this->currencyConvertToBaseCurrency($taxAmount);

            $this->setBaseTaxAmount($baseTaxAmount)
                 ->setTaxAmount($taxAmount)
                 ->setBaseTaxInvoiced(0)
                 ->setTaxInvoiced(0);
        }

        // discount tax compensation amount
        if (isset($this->newParams['discount_tax_compensation_amount'])) {
            $hiddenTax     = $this->newParams['discount_tax_compensation_amount'];
            $baseHiddenTax = $this->currencyConvertToBaseCurrency($hiddenTax);

            $this->setBaseDiscountTaxCompensationAmount($baseHiddenTax)
                 ->setDiscountTaxCompensationAmount($hiddenTax);
        }

        // tax percent
        if (isset($this->newParams['tax_percent'])) {
            $this->setTaxPercent($this->newParams['tax_percent']);
        }

        // price
        if (isset($this->newParams['price'])) {
            $price     = $this->newParams['price'];
            $basePrice = $this->currencyConvertToBaseCurrency($price);

            $this->setBasePrice($basePrice)
                 ->setPrice($price);
        }

        // Price includes tax
        if (isset($this->newParams['price_incl_tax'])) {
            $priceInclTax     = $this->newParams['price_incl_tax'];
            $basePriceInclTax = $this->currencyConvertToBaseCurrency($priceInclTax);

            $this->setBasePriceInclTax($basePriceInclTax)
                 ->setPriceInclTax($priceInclTax);
        }

        // discount amount
        if (isset($this->newParams['discount_amount'])) {
            $discountAmount     = $this->newParams['discount_amount'];
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
            $currentSubtotal           = $this->newParams['subtotal'];
            $baseCurrencySubtotal      = $this->currencyConvertToBaseCurrency($currentSubtotal);
            $roundBaseCurrencySubtotal = $this->helper->roundAndFormatPrice($baseCurrencySubtotal);

            $this->setBaseRowTotal($roundBaseCurrencySubtotal)
                 ->setRowTotal($currentSubtotal)
                 ->setBaseRowInvoiced(0)
                 ->setRowInvoiced(0);
        }

        // Subtotal includes tax
        if (isset($this->newParams['subtotal_incl_tax'])) {
            $subtotalInclTax                  = $this->newParams['subtotal_incl_tax'];
            $baseCurrencySubtotalInclTax      = $this->currencyConvertToBaseCurrency($subtotalInclTax);
            $roundBaseCurrencySubtotalInclTax = $this->helper->roundAndFormatPrice($baseCurrencySubtotalInclTax);

            $this->setBaseRowTotalInclTax($roundBaseCurrencySubtotalInclTax)
                 ->setRowTotalInclTax($subtotalInclTax);
        }

        $this->save();
    }

    /**
     * Convert currency
     *
     * @param float $basePrice
     * @return float
     */
    protected function currencyConvert($basePrice)
    {
        $baseCurrency  = $this->getOrder()->getBaseCurrencyCode();
        $orderCurrency = $this->getOrder()->getOrderCurrencyCode();
        if ($baseCurrency === $orderCurrency) {
            return $basePrice;
        }

        return $this->directoryHelper
            ->currencyConvert($basePrice, $baseCurrency, $orderCurrency);
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

    /**
     * Getter decreasedQty
     *
     * @return float
     */
    public function getDecreasedQty()
    {
        return $this->decreasedQty;
    }

    /**
     * Getter increasedQty
     *
     * @return float
     */
    public function getIncreasedQty()
    {
        return $this->increasedQty;
    }

    /**
     * Get Order Item Row Total
     *
     * @param null|Item $item
     * @return float
     */
    protected function getOrderItemRowTotal($item = null)
    {
        if ($item === null) {
            $item = $this;
        }

        if (is_array($item)) {
            $item = new DataObject($item);
        }

        return $item->getRowTotal()
            + $item->getTaxAmount()
            + $item->getWeeeTaxAppliedRowAmount()
            - $item->getDiscountAmount();
    }
}
