<?php
namespace Webfitters\Custom\Block;

class ShowNutrition extends \Magento\Framework\View\Element\Template {

	private $registry;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
	) {
		$this->registry=$registry;
		parent::__construct($context, $data);
	}

	public function getProduct() {
        $product = $this->registry->registry('product');
        return $product ? $product : null;
    }

}