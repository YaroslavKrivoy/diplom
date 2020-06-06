<?php
namespace Webfitters\PurchaseOrder\Plugin\Order;

class Convert {

    public function aroundToInvoice(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order $order){
        $invoice = $proceed($order);
        $invoice->setPoNumber($order->getPoNumber());
        return $invoice;
    }

}