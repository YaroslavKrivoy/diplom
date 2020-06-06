<?php
namespace Webfitters\DeliveryDate\Plugin\Order;

class Convert {

    public function aroundToInvoice(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order $order){
        $invoice = $proceed($order);
        $invoice->setDeliveryDate($order->getDeliveryDate());
        $invoice->setCustomerId($order->getCustomerId());
        return $invoice;
    }

    public function aroundToShipment(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order $order){
    	$shipment = $proceed($order);
        $shipment->setCustomerId($order->getCustomerId());
        return $shipment;
    }

}