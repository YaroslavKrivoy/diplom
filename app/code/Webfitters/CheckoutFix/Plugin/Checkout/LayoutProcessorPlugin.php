<?php
namespace Webfitters\CheckoutFix\Plugin\Checkout;

class LayoutProcessorPlugin {

	protected $session;

	public function __construct(
		\Magento\Checkout\Model\Session $session
	){
		$this->session = $session;
	}

	public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout){
		if(!$this->session->getQuote()->getIsVirtual()){
			$billingAddressForm = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'];
			$emailForm = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email'];
			$emailForm['sortOrder'] = 0;
			$billingAddressForm['children']['form-fields']['children']['customer-email'] = $emailForm;
	        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['billing-address-form'] = $billingAddressForm;
	        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['customer-email']);
	        unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']);
	    } else {
	    	$billingAddressForm = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'];
	    	$billingAddressForm['sortOrder'] = 0;
	    	$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['sortOrder'] = 1;

			$emailForm = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['customer-email'];
			$emailForm['sortOrder'] = 0;
			$billingAddressForm['children']['form-fields']['children']['customer-email'] = $emailForm;
			$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['billing-address-form'] = $billingAddressForm;
			unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['customer-email']);
	        unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']);
	        //dump($jsLayout);
	        //die();
	    }
        return $jsLayout;
	}

}