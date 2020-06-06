<?php 
namespace Webfitters\Wholesale\Model;

class Product extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

	const CACHE_TAG = 'webfitters_wholesale_product';

	protected function _construct() {
		$this->_init('Webfitters\Wholesale\Model\ResourceModel\Product');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

}