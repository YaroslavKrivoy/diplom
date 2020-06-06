<?php
namespace Webfitters\DeliveryDate\Observer;

class SaveDeliveryDate implements \Magento\Framework\Event\ObserverInterface {

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
        if($this->request->getParam('order')['delivery_date'] != ''){
            $order->setDeliveryDate(\Carbon\Carbon::parse($this->request->getParam('order')['delivery_date'])->toDateTimeString());
        } else {
            $order->setDeliveryDate(null);
        }
        $order->save();
    }
}