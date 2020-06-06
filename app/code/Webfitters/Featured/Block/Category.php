<?php
namespace Webfitters\Featured\Block;

class Category extends \Magento\Framework\View\Element\Template {

	protected $category;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $category,   
        array $data = []
    ){
		$this->category=$category;
		parent::__construct($context, $data);
	}

	public function getCategories(){
		$collection = $this->category->create()
			->addAttributeToFilter('is_active', 1)
			->addAttributeToFilter('featured', 1)
			->addAttributeToSelect('*');
		return $collection;
	}

}