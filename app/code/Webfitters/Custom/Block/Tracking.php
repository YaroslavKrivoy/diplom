<?php
namespace Webfitters\Custom\Block;

class Tracking extends \Magento\Framework\View\Element\Template {

	protected $session;
	protected $category;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Model\CategoryFactory $category,
        array $data = []
	) {
		parent::__construct($context, $data);
		$this->session = $session;
		$this->category = $category;
	}

	public function getOrder(){
		return $this->session->getLastRealOrder();
	}

	public function getCategory($product){
		$ids = $product->getCategoryIds();
		$category = $this->category->create()->load(array_pop($ids));
		return $category->getName();
	}

}