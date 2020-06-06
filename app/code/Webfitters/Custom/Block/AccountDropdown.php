<?php
namespace Webfitters\Custom\Block;

class AccountDropdown extends \Magento\Framework\View\Element\Template {

	protected $store;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $store,
		array $data=[]
	){
		$this->store=$store;
		parent::__construct($context, $data);
	}

	public function getMediaUrl(){
		return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}

}