<?php
namespace Webfitters\Custom\Block;

class Categories extends \Magento\Framework\View\Element\Template {

	protected $category;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $category,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->category = $category;
	}

	public function getCategories(){
		return $this->category->create()->addAttributeToSelect('*');
	}

}