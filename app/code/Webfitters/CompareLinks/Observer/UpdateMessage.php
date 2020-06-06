<?php
namespace Webfitters\CompareLinks\Observer;

class UpdateMessage implements \Magento\Framework\Event\ObserverInterface {
    
    protected $messages;
    protected $url;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messages,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->messages = $messages;
        $this->url = $url;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $messages = $this->messages->getMessages(true);
        $this->messages->addSuccess(
            $messages->getLastAddedMessage()->getText().
            '<a href="'.$this->url->getUrl('catalog/product_compare/index').'" class="pull-right">Go To Compare List</a>'
        );
    }
}