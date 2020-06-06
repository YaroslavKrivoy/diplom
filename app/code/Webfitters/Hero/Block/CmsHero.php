<?php
namespace Webfitters\Hero\Block;

class CmsHero extends \Magento\Framework\View\Element\Template {

	protected $store;
	protected $page;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Magento\Cms\Model\Page $page,
		array $data=[]
	) {
		$this->page = $page;
		$this->store = $store;
		parent::__construct($context, $data);
	}

	public function getImage(){
		$image = '';
		if($this->page->getHeroImage() != ''){
			$image = $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$this->page->getHeroImage();
		}
		return $image;
	}

	public function isParallax(){
		return (($this->page->getHeroParallax()==1)?true:false); 
	}

	public function getText(){
		return $this->page->getHeroText();
	}

	public function getTitle(){
		return $this->page->getTitle();
	}

	public function _prepareLayout(){
		if($this->page->getHeroImage() == ''){
    		$this->pageConfig->addBodyClass('no-hero');
    	}
    	return parent::_prepareLayout();
	}
	
}