<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Model;

class Converter extends \Magento\Sales\Model\Convert\Order
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Model\Order\Invoice\ItemFactory $invoiceItemFactory
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Sales\Model\Order\Creditmemo\ItemFactory $creditmemoItemFactory
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Framework\ObjectManagerInterface
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Model\Order\Invoice\ItemFactory $invoiceItemFactory,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Sales\Model\Order\Creditmemo\ItemFactory $creditmemoItemFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        parent::__construct(
            $eventManager,
            $invoiceRepository,
            $invoiceItemFactory,
            $shipmentRepository,
            $shipmentItemFactory,
            $creditmemoRepository,
            $creditmemoItemFactory,
            $objectCopyService,
            $data
        );
    }

    /**
     * Converting order object to quote object
     *
     * @param   \Magento\Sales\Model\Order $order
     * @return  \Magento\Quote\Model\Quote
     */
    public function orderToQuote(\Magento\Sales\Model\Order $order, $quote = null)
    {
        if (!($quote instanceof \Magento\Quote\Model\Quote)) {
            $quote = $this->objectManager->create('Magento\Quote\Model\Quote');
        }

        $quote->setStoreId($order->getStoreId())
            ->setOrderId($order->getId());

        $this->_objectCopyService->copyFieldsetToTarget(
            'sales_convert_order',
            'to_quote',
            $order,
            $quote
        );

        $this->_eventManager->dispatch('sales_convert_order_to_quote', ['order' => $order, 'quote' => $quote]);

        return $quote;
    }

    /**
     * Convert quote item to order item.
     * Most part of the code was taken from Mage_Sales_Model_Convert_Quote::itemToOrderItem()
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem  $item
     * @param null|\Magento\Sales\Model\Order\Item          $orderItem
     * @return \Magento\Sales\Model\Order\Item
     */
    public function itemToOrderItem(\Magento\Quote\Model\Quote\Item\AbstractItem $item, $orderItem = null)
    {
        if ($orderItem === null) {
            $orderItem = $this->objectManager->create('\Magento\Sales\Model\Order\Item');
        }

        $orderItem->setStoreId($item->getStoreId())
            ->setQuoteItemId($item->getId())
            ->setQuoteParentItemId($item->getParentItemId())
            ->setProductId($item->getProductId())
            ->setProductType($item->getProductType())
            ->setQtyBackordered($item->getBackorders())
            ->setProduct($item->getProduct())
            ->setBaseOriginalPrice($item->getBaseOriginalPrice());

        $convertedOrderItem = $this->objectManager->get('\Magento\Quote\Model\Quote\Item\ToOrderItem')->convert($item);
        if ($orderItem) {
            foreach ($convertedOrderItem->getData() as $key => $value) {
                $orderItem->setData($key, $value);
            }
        }

        $this->_eventManager->dispatch(
            'sales_convert_quote_item_to_order_item',
            ['order_item' => $orderItem, 'item' => $item]
        );
        return $orderItem;
    }

    /**
     * Convert order payment to quote payment
     *
     * @param   \Magento\Sales\Model\Order\Payment $payment
     * @return  \Magento\Quote\Model\Quote\Payment
     */
    public function orderPaymentToQuotePayment(\Magento\Sales\Model\Order\Payment $payment, $quotePayment = null)
    {
        if (!($quotePayment instanceof \Magento\Quote\Model\Quote\Payment)) {
            $quotePayment = $this->objectManager->create('Magento\Quote\Model\Quote\Payment');
        }

        $quotePayment->setStoreId($payment->getStoreId())
            ->setCustomerPaymentId($payment->getCustomerPaymentId());

        $this->_objectCopyService->copyFieldsetToTarget(
            'sales_convert_order_payment',
            'to_quote_payment',
            $payment,
            $quotePayment
        );

        return $quotePayment;
    }
}
