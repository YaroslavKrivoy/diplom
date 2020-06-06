<?php
namespace Webfitters\Checkout\Observer;

class SaveUsesContainer implements \Magento\Framework\Event\ObserverInterface {

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
        if(isset($this->request->getParam('order')['uses_container_fee']) && $this->request->getParam('order')['uses_container_fee'] == '1'){
        	$order->setUsesContainerFee(1);
            $order->setContainerFee($this->request->getParam('order')['container_fee']);
            $order->setContainerFeeAdditive((isset($this->request->getParam('order')['container_fee_additive']))?$this->request->getParam('order')['container_fee_additive']:null);
        } else {
            $order->setUsesContainerFee(null);
            $order->setContainerFee(null);
            $order->setContainerFeeAdditive(null);
        }
        $order->save();
    }
}