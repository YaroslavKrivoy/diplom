<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model\Edit;

use \Magento\Framework\DataObject;
use \Magento\Framework\Model;

class Quote
{
    /**
     * Order Editor helper
     *
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Order Editor converter
     *
     * @var \MageWorx\OrderEditor\Model\Converter
     */
    protected $converter;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \MageWorx\OrderEditor\Model\Quote
     */
    protected $quote;

    /**
     * @var \MageWorx\OrderEditor\Model\Quote\Item
     */
    protected $quoteItem;

    /**
     * @var \MageWorx\OrderEditor\Model\Order\Item
     */
    protected $orderItem;

    /**
     * @var \Magento\Quote\Model\Quote\Item\ToOrderItem
     */
    protected $quoteItemToOrderItem;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    protected $quoteItemCollectionFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\Store $store,
        \MageWorx\OrderEditor\Model\Converter $converter,
        \MageWorx\OrderEditor\Model\Quote $quote,
        \MageWorx\OrderEditor\Model\Quote\Item $quoteItem,
        \MageWorx\OrderEditor\Model\Order\Item $orderItem,
        \Magento\Quote\Model\Quote\Item\ToOrderItem $quoteItemToOrderItem,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->objectManager = $objectManager;
        $this->helperData = $helperData;
        $this->coreRegistry = $coreRegistry;
        $this->store = $store;
        $this->converter = $converter;
        $this->orderItem = $orderItem;
        $this->quote = $quote;
        $this->quoteItem = $quoteItem;
        $this->quoteItemToOrderItem = $quoteItemToOrderItem;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param string[] $params
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function createNewOrderItems($params, $order)
    {
        $params = $this->prepareParams($params);
        $quoteItems = $this->prepareNewQuoteItems($params, $order);

        $orderItems = [];
        foreach ($quoteItems as $quoteItem) {
            try {
                $orderItem = $this->quoteItemToOrderItem->convert($quoteItem);
                $orderItem->setItemId($quoteItem->getItemId());

                if ($quoteItem->getProductType() == 'bundle') {
                    $simpleOrderItems = $this->addSimpleItemsForBundle($quoteItem, $orderItem);
                    $orderItem->setChildrenItems($simpleOrderItems);
                }

                $orderItem->setMessage($quoteItem->getMessage());
                $orderItem->setHasError($quoteItem->getHasError());
                $orderItems[] = $orderItem;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $orderItems;
    }

    /**
     * @param int $itemId
     * @param string[] $params
     * @return \Magento\Sales\Api\Data\OrderItemInterface
     */
    public function createNewOrderItem($itemId, $params)
    {
        $orderItem = $this->orderItem->load($itemId);
        $params = $this->prepareProductOptions($orderItem, $params);
        $quoteItem = $this->convertOrderItemToQuoteItem($orderItem, $params);

        //$quoteItem->prepareQuoteItem();
        $quoteItemId = $orderItem->getQuoteItemId();
        $quoteItem->setItemId($quoteItemId);
        $quoteItem->save();

        return $this->quoteItemToOrderItem->convert(
            $quoteItem,
            ['parent_item' => $orderItem]
        );
    }

    /**
     * @param \MageWorx\OrderEditor\Model\Quote\Item $quoteItem
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    protected function addSimpleItemsForBundle($quoteItem, $orderItem)
    {
        $simpleOrderItems = [];
        $simpleQuoteItems = $quoteItem->getChildren();

        foreach ($simpleQuoteItems as $simpleQuoteItem) {
            /** @var $simpleQuoteItem \Magento\Quote\Model\Quote\Item */
            try {
                $simpleOrderItem = $this->quoteItemToOrderItem->convert($simpleQuoteItem);
                $simpleOrderItem->setItemId($simpleQuoteItem->getItemId());
                $simpleOrderItem->setParentItem($orderItem);

                $simpleOrderItem->setMessage($simpleQuoteItem->getMessage());
                $simpleOrderItem->setHasError($simpleQuoteItem->getHasError());
                $simpleOrderItem->setDiscountPercent($quoteItem->getDiscountPercent());
                //$simpleOrderItem->setTaxPercent($quoteItem->getTaxPercent());
                $simpleOrderItems[] = $simpleOrderItem;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $simpleOrderItems;
    }

    /**
     * @param string[] $params
     * @param \Magento\Sales\Model\Order $order
     * @return \MageWorx\OrderEditor\Model\Quote\Item []
     */
    protected function prepareNewQuoteItems($params, $order)
    {
        $quoteItems = [];

        $quote = $this->getQuoteByOrder($order);
        $quote->setAllItemsAreNew(true);

        // Prevent drop qty to null in case product is out of stock
        $quote->setIsSuperMode(true);
        $quote->setIgnoreOldQty(true);

        foreach ($params as $productId => $options) {
            try {
                $product = $this->prepareProduct($productId, $order->getStore());
                $config = new DataObject($options);
                $quoteItem = $quote->addProduct($product, $config);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                continue;
            }

            if (!empty($quoteItem)) {
                if (isset($options['bundle_option'])) {
                    $requestedOptions = count(
                        array_filter(
                            array_values($options['bundle_option']),
                            function ($value) {
                                return !empty($value) || $value === 0;
                            }
                        )
                    );

                    $addedOptions = count($quoteItem->getChildren());

                    if ($requestedOptions > $addedOptions) {
                        $quoteItem->setHasError(true);
                        $quoteItem->setMessage(
                            __('Not all selected products were added to the order as some products are currently unavailable.')
                        );
                    }
                }

                $quoteItem->save();
                $quoteItems[] = $quoteItem;
            }
        }

        $quote->collectTotals()->save();

        return $quoteItems;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param string[] $params
     * @return \MageWorx\OrderEditor\Model\Quote\Item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function convertOrderItemToQuoteItem($orderItem, $params)
    {
        $quoteItemId = $orderItem->getQuoteItemId();

        $quoteItem = $this->quoteItem->load($quoteItemId);
        $quote = $this->getQuoteByQuoteItem($quoteItem);

        $quoteItem->setQuote($quote);

        $quoteItem = $quote->updateItem($quoteItem, $params);

        $quote->collectTotals();

        return $quoteItem;
    }

    /**
     * @param int $productId
     * @param \Magento\Store\Model\Store $store
     * @return \Magento\Catalog\Model\Product
     * @throws \Exception
     */
    protected function prepareProduct($productId, $store)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
            ->setStore($store)
            ->setStoreId($store->getStoreId())
            ->load($productId);

        if (!$product->getId()) {
            throw new \Exception(
                __('An issue occurred when trying to add product ID %1 to the order.', $productId)
            );
        }

        return $product;
    }

    /**
     * @param string[] $params
     * @return string[]
     */
    protected function prepareParams($params)
    {
        $preparedParams = [];

        foreach ($params as $productId => $options) {
            if (isset($options['super_group'])) {
                foreach ($options['super_group'] as $id => $opt) {
                    if (is_string($opt) || is_numeric($opt)) {
                        $preparedParams[$id] = ['qty' => $opt];
                    }
                }
            } else {
                if (!empty($options)) {
                    $preparedParams[$productId] = $options;
                }
            }
        }

        return $preparedParams;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param string[] $params
     * @return string[]
     */
    protected function prepareProductOptions($orderItem, $params)
    {
        $params['product'] = $orderItem->getProductId();
        $params = $this->updateFiles($params, $orderItem->getItemId());
        return $params;
    }

    /**
     * @param string[] $options
     * @param int $itemId
     * @return string[]
     */
    protected function updateFiles($options, $itemId)
    {
        return $options;
    }

    /**
     * @param int $quoteItemId
     * @param null $params
     * @return \Magento\Sales\Api\Data\OrderItemInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUpdatedOrderItem($quoteItemId, $params = null)
    {
        $quoteItem = $this->getQuoteItemById($quoteItemId);

        if (!empty($params)) {
            $quote = $this->getQuoteByQuoteItem($quoteItem);
            $quoteItem = $quote->updateItem($quoteItem, $params);
            $quote->collectTotals();
        }

        $orderItem = $this->quoteItemToOrderItem->convert($quoteItem);
        $parentQuoteItemId = $quoteItem->getParentItemId();
        $parentOrderItemId = $this->getParentOrderItemId($parentQuoteItemId);

        $orderItem->setOriginalPrice($orderItem->getPrice());
        $orderItem->setBaseOriginalPrice($orderItem->getBasePrice());
        $orderItem->setParentItemId($parentOrderItemId);

        return $orderItem;
    }

    /**
     * @param int $parentQuoteItemId
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getParentOrderItemId($parentQuoteItemId)
    {
        if ($parentQuoteItemId) {
            $orderItem = $this->orderItem->getCollection()
                ->addFieldToFilter('quote_item_id', $parentQuoteItemId)
                ->getFirstItem();

            return $orderItem->getItemId();
        }

        return null;
    }

    /**
     * @param int $quoteItemId
     * @return \MageWorx\OrderEditor\Model\Quote\Item
     * @throws \Exception
     */
    protected function getQuoteItemById($quoteItemId)
    {
        $quoteItem = $this->quoteItem->load($quoteItemId);

        $quote = $this->getQuoteByQuoteItem($quoteItem);

        $quoteItems = $this->quoteItemCollectionFactory->create()->setQuote($quote);
        $quoteItems->load();

        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getItemId() == $quoteItemId) {
                return $quoteItem;
            }
        }

        throw new \Exception('Can not load quote item');
    }

    /**
     * @param  \MageWorx\OrderEditor\Model\Quote\Item $quoteItem
     * @return \MageWorx\OrderEditor\Model\Quote
     */
    protected function getQuoteByQuoteItem($quoteItem)
    {
        $storeId = $quoteItem->getStoreId();
        $store = $this->store->load($storeId);
        $quoteId = $quoteItem->getQuoteId();
        return $this->quote->setStore($store)->load($quoteId);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \MageWorx\OrderEditor\Model\Quote
     */
    protected function getQuoteByOrder($order)
    {
        $storeId = $order->getStoreId();
        $store = $this->store->load($storeId);
        $quoteId = $order->getQuoteId();
        return $this->quote->setStore($store)->load($quoteId);
    }
}
