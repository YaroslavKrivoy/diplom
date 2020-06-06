<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Address;
use MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\Collection;

/**
 * Class Synchronize
 * @package MageWorx\OrdersGrid\Observer
 *
 * Aggregated observer class for the automatically synchronization of additional table of the orders grid
 * All of the observed classes (entities) could be found in the
 * @see \MageWorx\OrdersGrid\Observer\Synchronize::CLASS_MAPPER
 */
class Synchronize implements ObserverInterface
{
    /**
     * Observable class names with corresponding methods.
     *
     */
    const CLASS_MAPPER = [
        'Magento\Sales\Model\Order' => 'syncOrderData',
        'Magento\Sales\Model\Order\Invoice' => 'syncInvoiceData',
        'Magento\Sales\Model\Order\Shipment\Track' => 'syncShipmentTrackData',
        'Magento\Sales\Model\Order\Shipment' => 'syncShipmentData',
        'Magento\Sales\Model\Order\Address' => 'syncAddressData',
        'Magento\Sales\Model\Order\Item' => 'syncItemsData',
        'Magento\Sales\Model\Order\Tax' => 'syncTaxData'
    ];

    /**
     * @var Collection
     */
    private $customOrderGridCollection;

    /**
     * Synchronize constructor.
     * @param Collection $customOrderGridCollection
     */
    public function __construct(
        Collection $customOrderGridCollection
    ) {
        $this->customOrderGridCollection = $customOrderGridCollection;
    }

    /**
     * For the orders:
     * Used on {model_event_prefix}_after_commit_callback
     * @see \Magento\Framework\Model\AbstractModel::afterCommitCallback()
     *
     * For others:
     * Used on {model_event_prefix}_save_after
     * @see \Magento\Framework\Model\AbstractModel::afterSave()
     *
     * @important The Item & Address does not trigger full order synchronization process because it causes transaction
     * errors and breaks a checkout
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @see \Magento\Framework\Model\AbstractModel::_getEventData() */
        $object = $event->getDataObject();
        if (!is_object($object)) {
            return $this;
        }

        foreach (static::CLASS_MAPPER as $className => $method) {
            if ($object instanceof $className) {
                $this->{$method}($object);
            }
        }

        return $this;
    }

    /**
     * Sync order data: update existing info or insert new info
     * [coupon_code, weight, subtotal_purchased]
     *
     * @param \Magento\Sales\Model\Order $order
     */
    private function syncOrderData(\Magento\Sales\Model\Order $order)
    {
        $orderId = $order->getId();
        if ($this->customOrderGridCollection->isOrderExists($orderId)) {
            // UPDATE existing order
            $this->customOrderGridCollection->grabDataFromSalesOrderTable([$orderId]);
        } else {
            // INSERT new order
            $this->customOrderGridCollection->syncOrdersData([$orderId]);
        }
    }

    /**
     * Sync invoice data
     * [invoices]
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     */
    private function syncInvoiceData(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $orderId = $invoice->getOrderId();
        if ($this->customOrderGridCollection->isOrderExists($orderId)) {
            // UPDATE existing order data with invoice data
            $this->customOrderGridCollection->grabDataFromInvoiceTable([$orderId]);
        } else {
            // INSERT new order with invoice data
            $this->customOrderGridCollection->syncOrdersData([$orderId]);
        }
    }

    /**
     * Sync shipment track data
     * [tracking_number]
     *
     * @param \Magento\Sales\Model\Order\Shipment\Track $shipmentTrack
     */
    private function syncShipmentTrackData(\Magento\Sales\Model\Order\Shipment\Track $shipmentTrack)
    {
        $orderId = $shipmentTrack->getOrderId();
        if ($this->customOrderGridCollection->isOrderExists($orderId)) {
            // UPDATE existing order data with shipment data
            $this->customOrderGridCollection->grabDataFromTrackShipmentTable([$orderId]);
        } else {
            // INSERT new order with shipment data
            $this->customOrderGridCollection->syncOrdersData([$orderId]);
        }
    }

    /**
     * Sync shipment data
     * [shipments]
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     */
    private function syncShipmentData(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        $orderId = $shipment->getOrderId();
        if ($this->customOrderGridCollection->isOrderExists($orderId)) {
            // UPDATE existing order data with shipment data
            $this->customOrderGridCollection->grabDataFromShipmentTable([$orderId]);
        } else {
            // INSERT new order with shipment data
            $this->customOrderGridCollection->syncOrdersData([$orderId]);
        }
    }

    /**
     * Sync tax data:
     *
     * [
     *      'applied_tax_code',
     *      'applied_tax_percent',
     *      'applied_tax_amount',
     *      'applied_tax_base_amount',
     *      'applied_tax_base_real_amount'
     * ]
     *
     * @param \Magento\Sales\Model\Order\Tax $tax
     * @deprecated Because the tax model has no own event prefix!
     * @see \Magento\Sales\Model\Order\Tax::_eventPrefix
     */
    private function syncTaxData(\Magento\Sales\Model\Order\Tax $tax)
    {
        $orderId = $tax->getOrderId();
        if ($this->customOrderGridCollection->isOrderExists($orderId)) {
            // UPDATE existing order data with tax data
            $this->customOrderGridCollection->grabDataFromSalesOrderTaxTable([$orderId]);
        } else {
            // INSERT new order with shipment data
            $this->customOrderGridCollection->syncOrdersData([$orderId]);
        }
    }

    /**
     * Sync addresses data
     * [country_id, region, fax, telephone, postcode, city]
     *
     * @param \Magento\Sales\Model\Order\Address $address
     */
    private function syncAddressData(\Magento\Sales\Model\Order\Address $address)
    {
        $orderId = $address->getParentId();
        $addressType = $address->getAddressType();
        $orderExists = $this->customOrderGridCollection->isOrderExists($orderId);
        if (!$orderExists) {
            /**
             * Do nothing in case order is not exists yet
             * In this case data would be synchronized with the whole order in the method:
             * @see \MageWorx\OrdersGrid\Observer\Synchronize::syncOrderData()
             */
            return;
        }

        switch ($addressType) {
            case Address::TYPE_BILLING:
                $this->customOrderGridCollection->grabDataFromOrderBillingAddressTable([$orderId]);
                break;
            case Address::TYPE_SHIPPING:
                $this->customOrderGridCollection->grabDataFromOrderShippingAddressTable([$orderId]);
                break;
            default:
                /**
                 * This is a not usual case, when address type is not in one of this types: billing or shipping
                 */
                break;
        }
    }

    /**
     * Sync order items data
     * [product_name, product_sku]
     *
     * @param \Magento\Sales\Model\Order\Item $item
     */
    private function syncItemsData(\Magento\Sales\Model\Order\Item $item)
    {
        $orderId = $item->getOrderId();
        if ($this->customOrderGridCollection->isOrderExists($orderId)) {
            // UPDATE existing order data with invoice data
            $this->customOrderGridCollection->grabDataFromOrderItemsTable([$orderId]);
        }
        /**
         * Do nothing in case order is not exists yet
         * In this case data would be synchronized with the whole order in the method:
         * @see \MageWorx\OrdersGrid\Observer\Synchronize::syncOrderData()
         */
    }
}
