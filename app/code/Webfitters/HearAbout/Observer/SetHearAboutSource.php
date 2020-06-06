<?php
namespace Webfitters\HearAbout\Observer;

class SetHearAboutSource implements \Magento\Framework\Event\ObserverInterface {

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
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $quote->setHearAboutId($this->request->getParam('order')['hearabout_id']);
        $order->setHearAboutId($this->request->getParam('order')['hearabout_id']);
        $quote->save();
        $order->save();
    }
}