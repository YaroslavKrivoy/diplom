<?php
namespace Webfitters\Perishable\Observer;

class ValidatePerishable implements \Magento\Framework\Event\ObserverInterface {
    
    protected $messages;
    protected $url;
    protected $cart;
    protected $config;
    protected $response;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messages,
        \Magento\Framework\App\ResponseFactory $response,
        \Magento\Framework\UrlInterface $url,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->config = $config;
        $this->cart = $cart;
        $this->messages = $messages;
        $this->url = $url;
        $this->response = $response;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $amount = 0;
        $hasPerishable = false;
        $items = $this->cart->getQuote()->getItemsCollection();
        foreach($items as $item){
            if($item->getProduct()->getPerishable()){
                $hasPerishable = true;
                $amount += ($item->getQty() * $item->getWeight());
            }
        }
        $min = floatval($this->config->getValue('perishable/general/min_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        if($hasPerishable && $amount < $min){
            $this->messages->addError('Unfortunately, we have a minimum order amount of '.$min.' lbs. for perishable goods. Please purchase more or remove them from your cart.');
            $this->response->create()->setRedirect($this->url->getUrl('checkout/cart/index'))->sendResponse();
            die();
        }
        return $this;
    }

}