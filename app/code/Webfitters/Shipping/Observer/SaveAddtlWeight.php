<?php
namespace Webfitters\Shipping\Observer;

class SaveAddtlWeight implements \Magento\Framework\Event\ObserverInterface {

    protected $quote;
    protected $config;

    public function __construct(
    	\Magento\Quote\Model\QuoteFactory $quote,
    	\Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->quote = $quote;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getOrder();
        $quote = $this->quote->create()->load($order->getQuoteId());
        if(strpos($order->getShippingMethod(), 'ups_') !== false){
        	$data = json_decode($quote->getUpsData());
        	if($data){
	        	$weights = [
	        		1 => $this->config->getValue('webfitters_shipping/general/one_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), 
	                2 => $this->config->getValue('webfitters_shipping/general/two_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), 
	                3 => $this->config->getValue('webfitters_shipping/general/three_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), 
	                4 => $this->config->getValue('webfitters_shipping/general/four_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
	        	];
	        	$method = str_replace('ups_', '', $order->getShippingMethod());
	        	
	        	foreach($data as $rate){
	        		if($rate->Code == $method){
	        			if(isset($rate->TimeInTransit)){
	        				$days = $rate->TimeInTransit;
	        			} else {
	        				$d = (array)$rate->GuaranteedDaysToDelivery;
		        			$days = (!empty($d))?(int)$d[0]:4;
		        		}
		        		$order->setTimeInTransit($days);
	        			$order->setUpsData($quote->getUpsData());
	        			$order->setAdditionalWeight(isset($weights[$days])?$weights[$days]:$weights[4]);
	        			$order->save();
	        		}
	        	}
	        }
        }
    }

}