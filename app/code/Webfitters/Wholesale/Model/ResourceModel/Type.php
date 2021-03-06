<?php
namespace Webfitters\Wholesale\Model\ResourceModel;


class Type extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
	public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
		parent::__construct($context);
	}
	
	protected function _construct() {
		$this->_init('wf_wholesale_types', 'id');
	}
	
}