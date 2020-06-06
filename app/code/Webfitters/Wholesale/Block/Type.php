<?php
namespace Webfitters\Wholesale\Block;

class Type extends \Magento\Framework\View\Element\Template {
	
	protected $store;
	protected $type;
	protected $registry;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Webfitters\Wholesale\Model\TypeFactory $type,
		\Magento\Framework\Registry $registry,
		\Magento\Store\Model\StoreManagerInterface $store,
		array $data=[]
	) {
		parent::__construct($context, $data);
		$this->store = $store;
		$this->type = $type;
		$this->registry = $registry;
	}

	public function getType(){
		return $this->registry->registry('type');
	}
	
	public function getImageUrl($path){
		return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'webfitters/wholesale/'.$path;
	}

	public function getTypes(){
		return $this->type->create()->getCollection();
	}

	public function getImage(){
		return $this->getImageUrl($this->getType()->getHero());
	}

	public function isParallax(){
		return true; 
	}

	public function getText(){
		return 'Wholesale: '.$this->getType()->getTitle();
	}

	public function getTitle(){
		return $this->getText();
	}

}