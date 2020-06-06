<?php
namespace Webfitters\Checkout\Ui\DataProvider\Product;

class AdvancedPricing extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AdvancedPricing {

	public function modifyMeta(array $meta) {
		$meta = parent::modifyMeta($meta);
        if(isset($meta['advanced_pricing_modal'])){
            if (isset($this->meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['tier_price'])) {
                $row = &$this->meta['advanced_pricing_modal']['children']['advanced-pricing']['children']['tier_price']['children']['record']['children'];
            }
    		$row['value_lb'] = [
    			'arguments' => [
    				'data' => [
    					'config' => [
    						'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'formElement' => \Magento\Ui\Component\Form\Element\Input::NAME,
                            'dataType' => \Magento\Ui\Component\Form\Element\DataType\Price::NAME,
                            'label' => __('Price/lb'),
                            'enableLabel' => true,
                            'dataScope' => 'value_lb',
                            'addbefore' => $this->locator->getStore()->getBaseCurrency()->getCurrencySymbol(),
                            'sortOrder' => 45,
                            'validation' => [
                                'required-entry' => false,
                                'validate-greater-than-zero' => false,
                                'validate-number' => true,
                            ],
                            'imports' => [
                                'priceValue' => '${ $.provider }:data.product.price_lb',
                            ]
    					]
    				]
    			]
    		];
        }
		return $this->meta;
	}

}