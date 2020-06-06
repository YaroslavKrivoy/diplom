<?php
namespace Webfitters\Checkout\Plugin\Order;

class Convert {

    public function aroundToInvoice(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order $order){
        $invoice = $proceed($order);
        $invoice->setUsesContainerFee($order->getUsesContainerFee());
        $invoice->setContainerFeeAdditive($order->getContainerFeeAdditive());
        $invoice->setContainerFee($order->getContainerFee());
        return $invoice;
    }

    public function aroundToShipment(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order $order){
        $shipment = $proceed($order);
        $shipment->setUsesContainerFee($order->getUsesContainerFee());
        $shipment->setContainerFeeAdditive($order->getContainerFeeAdditive());
        $shipment->setContainerFee($order->getContainerFee());
        return $shipment;
    }

}