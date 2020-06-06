<?php
namespace Webfitters\HearAbout\Plugin\Checkout;

class LayoutProcessorPlugin {

	protected $hearabout;

	public function __construct(
		\Webfitters\HearAbout\Model\HearAboutFactory $hearabout
	) {
		$this->hearabout = $hearabout;
	}

	public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout){
		$sources = [['value' => '', 'label' => 'Please Choose']];
		foreach($this->hearabout->create()->getCollection()->setOrder('name', 'asc') as $source){
			$sources[] = [
				'value' => $source->getId(),
				'label' => $source->getName()
			];
		}
		$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['billing-address-form']['children']['form-fields']['children']['hear_about'] = [
		    'component' => 'Magento_Ui/js/form/element/select',
		    'config' => [
		        'customScope' => '',
		        'customEntry' => null,
		        'template' => 'ui/form/field',
		        'elementTmpl' => 'ui/form/element/select'
		    ],
		    'dataScope' => 'hear_about',
		    'label' => 'Where did you hear about us?',
		    'provider' => 'checkoutProvider',
		    'sortOrder' => 1000,
		    'validation' => [
		       'required-entry' => true
		    ],
		    'options' => $sources,
		    'filterBy' => null,
		    'customEntry' => null,
		    'visible' => true,
		];
        return $jsLayout;
	}

}