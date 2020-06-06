<?php
namespace Webfitters\CartLinks\Observer;

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
            (($messages->getLastAddedMessage())?$messages->getLastAddedMessage()->getText():'').
            '<script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery("#product-success-modal").fadeIn();
                });
            </script>'
        );
    }
}