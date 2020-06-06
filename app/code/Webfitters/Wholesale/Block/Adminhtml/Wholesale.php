<?php
namespace Webfitters\Wholesale\Block\Adminhtml;

class Wholesale extends \Magento\Backend\Block\Template {

	protected $store;
	protected $types;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Webfitters\Wholesale\Model\TypeFactory $types,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->store = $store;
		$this->types = $types;
	}

	public function getTypes(){
		return $this->types->create()->getCollection();
	}

	public function getImageUrl($file){
 		return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'webfitters/wholesale/'.$file;
	}

}