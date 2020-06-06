<?php
namespace Webfitters\Pdf\Model\ResourceModel;


class Pdf extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
	public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
		parent::__construct($context);
	}
	
	protected function _construct() {
		$this->_init('wf_pdf', 'id');
	}
	
}