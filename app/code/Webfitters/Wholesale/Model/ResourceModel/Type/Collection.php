<?php
namespace Webfitters\Wholesale\Model\ResourceModel\Type;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'webfitters_wholesale_type_collection';
	protected $_eventObject = 'type_collection';

	protected function _construct() {
		$this->_init('Webfitters\Wholesale\Model\Type', 'Webfitters\Wholesale\Model\ResourceModel\Type');
	}

}