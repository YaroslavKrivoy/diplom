<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Infomodus\Upslabel\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
    protected $_coreRegistry;
    protected $_handy;
    protected $items;

    /**
     * OrderPlaceAfter constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Infomodus\Upslabel\Helper\Handy $handy
     * @param \Infomodus\Upslabel\Model\Items $items
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Infomodus\Upslabel\Helper\Handy $handy,
        \Infomodus\Upslabel\Model\Items $items
    )
    {
        $this->_coreRegistry = $registry;
        $this->_handy = $handy;
        $this->items = $items;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getId()) {
            //order not saved in the database
            return $this;
        }

        $storeId = $order->getStoreId();
        if ($this->_handy->_conf->getStoreConfig('upslabel/frontend_autocreate_label/frontend_order_autocreate_label_enable', $storeId) == 1) {
            $shippingActiveMethods = trim($this->_handy->_conf->getStoreConfig('upslabel/frontend_autocreate_label/apply_to', $storeId), " ,");
            $shippingActiveMethods = strlen($shippingActiveMethods) > 0 ? explode(",", $shippingActiveMethods) : [];
            $orderStatuses = explode(",", trim($this->_handy->_conf->getStoreConfig('upslabel/frontend_autocreate_label/orderstatus', $storeId), " ,"));
            if (isset($shippingActiveMethods)
                && count($shippingActiveMethods) > 0
                && in_array($order->getShippingMethod(), $shippingActiveMethods)
                && isset($orderStatuses)
                && count($orderStatuses) > 0
                && in_array($order->getStatus(), $orderStatuses)
            ) {
                $label = $this->items->getCollection()
                    ->addFieldToFilter('type', 'shipment')
                    ->addFieldToFilter('lstatus', 0)
                    ->addFieldToFilter('order_id', $order->getId());
                if (count($label) == 0) {
                    $shipment = $order->getShipmentsCollection();
                    $shipmentId = null;
                    if (count($shipment) > 0) {
                        $shipment = $shipment->getFirstItem();
                        $shipmentId = $shipment->getId();
                    }

                    $this->_handy->intermediate($order->getId(), 'shipment');
                    $this->_handy->defConfParams['package'] = $this->_handy->defPackageParams;
                    $this->_handy->getLabel(null, 'shipment', $shipmentId, $this->_handy->defConfParams);
                }
            }
        }

        return $this;
    }
}
