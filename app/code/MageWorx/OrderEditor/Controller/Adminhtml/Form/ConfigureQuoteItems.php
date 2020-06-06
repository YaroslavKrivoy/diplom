<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use MageWorx\OrderEditor\Model\Quote\Item;
use Magento\Sales\Controller\Adminhtml\Order\Create;
use Magento\Framework\DataObject;

class ConfigureQuoteItems extends Create
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        // Prepare data
        $configureResult = new DataObject();
        try {
            $quoteItem = $this->getQuoteItem();
            $quoteItemId = $quoteItem->getItemId();

            $configureResult->setOk(true);

            $options = $this->_objectManager
                ->create('Magento\Quote\Model\Quote\Item\Option')
                ->getCollection()
                ->addItemFilter([$quoteItemId])
                ->getOptionsByItem($quoteItem);
            $quoteItem->setOptions($options);

            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setProductId($quoteItem->getProductId());
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        return $this->_objectManager
            ->get('Magento\Catalog\Helper\Product\Composite')
            ->renderConfigureResult($configureResult);
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    protected function getQuoteItem()
    {
        $orderItemId = $this->getRequest()->getParam('id');
        if (!$orderItemId) {
            throw new \Exception(__('Order item id is not received.'));
        }

        $prefixIdLength = strlen(Item::PREFIX_ID);
        if (substr($orderItemId, 0, $prefixIdLength) == Item::PREFIX_ID) {
            $quoteItemId = substr(
                $orderItemId,
                $prefixIdLength,
                strlen($orderItemId)
            );
        } else {
            $orderItem = $this->loadOrderItem($orderItemId);
            $quoteItemId = $orderItem->getQuoteItemId();
        }

        return $this->loadQuoteItem($quoteItemId);
    }

    /**
     * @param int $quoteItemId
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    protected function loadQuoteItem($quoteItemId)
    {
        $quoteItem = $this->_objectManager
            ->create('Magento\Quote\Model\Quote\Item')
            ->load($quoteItemId);

        if (!$quoteItem->getId()) {
            throw new \Exception(__('Quote item is not loaded.'));
        }

        return $quoteItem;
    }

    /**
     * @param int $orderItemId
     * @return \Magento\Sales\Model\Order\Item
     * @throws \Exception
     */
    protected function loadOrderItem($orderItemId)
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $this->_objectManager
            ->create('Magento\Sales\Model\Order\Item')
            ->load($orderItemId);

        if (!$orderItem->getId()) {
            throw new \Exception(__('Order item is not loaded.'));
        }

        return $orderItem;
    }
}
