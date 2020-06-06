<?php
namespace Webfitters\OrderCredit\Plugin\Order;

class Convert {

    public function aroundToInvoice(\Magento\Sales\Model\Convert\Order $subject, \Closure $proceed, \Magento\Sales\Model\Order $order){
        $invoice = $proceed($order);
        $invoice->setBaseCreditAmount($order->getBaseCreditAmount());
        $invoice->setCreditAmount($order->getCreditAmount());
        return $invoice;
    }

}