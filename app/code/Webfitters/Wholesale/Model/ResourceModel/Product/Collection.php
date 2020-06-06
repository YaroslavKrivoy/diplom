<?php
namespace Webfitters\Wholesale\Model\ResourceModel\Product;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'webfitters_wholesale_product_collection';
	protected $_eventObject = 'product_collection';

	protected function _construct() {
		$this->_init('Webfitters\Wholesale\Model\Product', 'Webfitters\Wholesale\Model\ResourceModel\Product');
	}

}