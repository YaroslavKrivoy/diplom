<?php
namespace Webfitters\Wholesale\Block;

class Types extends \Magento\Framework\View\Element\Template {
	
	protected $store;
	protected $type;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Webfitters\Wholesale\Model\TypeFactory $type,
		\Magento\Store\Model\StoreManagerInterface $store,
		array $data=[]
	) {
		parent::__construct($context, $data);
		$this->store = $store;
		$this->type = $type;
	}

	public function getImageUrl($path){
		return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'webfitters/wholesale/'.$path;
	}

	public function getTypes(){
		return $this->type->create()->getCollection();
	}

}