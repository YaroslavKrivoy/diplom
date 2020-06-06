<?php
namespace Webfitters\OrderCredit\Observer;

class SetOrderCredit implements \Magento\Framework\Event\ObserverInterface {

    protected $order;
    protected $request;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->order = $order;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $credit = floatval($this->request->getParam('order')['order_credit']);
        $order = $observer->getOrder();
        /*$order->setBaseTotalDue($order->getBaseGrandTotal());
        $order->setTotalDue($order->getGrandTotal());*/
        $order->setBaseCreditAmount($credit);
        $order->setCreditAmount($credit);
        $order->save();
        /*$order->setGrandTotal($order->getGrandTotal() + $credit);
        $order->setBaseGrandTotal($order->getBaseGrandTotal() + $credit);
        $order->setBaseTotalPaid($credit);
        $order->setTotalPaid($credit);*/
        //$order->save();
    }
}