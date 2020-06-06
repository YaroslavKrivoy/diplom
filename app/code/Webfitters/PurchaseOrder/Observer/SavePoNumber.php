<?php
namespace Webfitters\PurchaseOrder\Observer;

class SavePoNumber implements \Magento\Framework\Event\ObserverInterface {

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
        $order = $observer->getOrder();
        $order->setPoNumber($this->request->getParam('order')['po_number']);
        $order->save();
    }
}