<?php
namespace Webfitters\CheckoutFix\Plugin\Cart;

class LayoutProcessorPlugin {

	public function afterProcess(\Magento\Checkout\Block\Cart\LayoutProcessor $subject, array $layout){
		$city = $layout['components']['block-summary']['children']['block-shipping']['children']['address-fieldsets']['children']['city'];
		$city['visible'] = true;
		$city['component'] = 'Magento_Ui/js/form/element/abstract';
		$city['config'] = [
			'customScope' => 'shippingAddress',
			'template' => 'ui/form/field',
			'elementTmpl' => 'ui/form/element/input'
		];
		$city['dataScope'] = 'shippingAddress.city';
		$city['label'] = __('City');
		$city['provider'] = 'checkoutProvider';
		$city['sortOrder'] = 111;
		$city['validation'] = [];
		$city['options'] = [];
		$city['filterBy'] = null;
		$city['customEntry'] = null;
		$layout['components']['block-summary']['children']['block-shipping']['children']['address-fieldsets']['children']['city'] = $city;
		return $layout;
	}

}