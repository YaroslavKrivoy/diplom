<?php
namespace Webfitters\Wholesale\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'webfitters_wholesale_category_collection';
	protected $_eventObject = 'category_collection';

	protected function _construct() {
		$this->_init('Webfitters\Wholesale\Model\Category', 'Webfitters\Wholesale\Model\ResourceModel\Category');
	}

}