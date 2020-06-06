<?php
namespace Webfitters\Hero\Block;

class CategoryHero extends \Magento\Framework\View\Element\Template {

	protected $store;
	protected $registry;
	protected $category;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Magento\Framework\Registry $registry,
		array $data=[]
	) {
		$this->registry = $registry;
		$this->store = $store;
		$this->category = $this->registry->registry('current_category');
		parent::__construct($context, $data);
	}

	public function getImage(){
		$image = '';
		if($this->category->getHeroImage() != ''){
			$image = $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/category/'.$this->category->getHeroImage();
		}
		return $image;
	}

	public function isParallax(){
		return (($this->category->getHeroParallax()==1)?true:false); 
	}

	public function getText(){
		return $this->category->getHeroTitle();
	}

	public function getTitle(){
		return $this->category->getName();
	}

	public function _prepareLayout(){
		if($this->category->getHeroImage() == ''){
    		$this->pageConfig->addBodyClass('no-hero');
    	}
    	return parent::_prepareLayout();
	}

}