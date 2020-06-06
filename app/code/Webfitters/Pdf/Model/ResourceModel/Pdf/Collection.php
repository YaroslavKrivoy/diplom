<?php
namespace Webfitters\Pdf\Model\ResourceModel\Pdf;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'webfitters_pdf_pdf_collection';
	protected $_eventObject = 'pdf_collection';

	protected function _construct() {
		$this->_init('Webfitters\Pdf\Model\Pdf', 'Webfitters\Pdf\Model\ResourceModel\Pdf');
	}

}