<?php 
namespace Webfitters\Pdf\Model;

class Pdf extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {

	const CACHE_TAG = 'webfitters_pdf';

	protected function _construct() {
		$this->_init('Webfitters\Pdf\Model\ResourceModel\Pdf');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

}