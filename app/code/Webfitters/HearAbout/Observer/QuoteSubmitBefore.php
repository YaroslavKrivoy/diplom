<?php
namespace Webfitters\HearAbout\Observer;

class QuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface {

    protected $quote;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quote
    ) {
        $this->quote = $quote;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getOrder();
        $quote = $this->quote->get($order->getQuoteId());
        $order->setHearAboutId($quote->getHearAboutId());
        $order->save();
    }

}