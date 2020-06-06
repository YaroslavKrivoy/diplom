<?php
namespace Webfitters\Checkout\Model\Payment;

class Purchaseorder extends \Magento\OfflinePayments\Model\Purchaseorder {

	public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = NULL){
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$area = $om->get('Magento\Framework\App\State');
		$areaCode= $area->getAreaCode();
		if($areaCode != 'adminhtml'){
			return false;
		}
		return parent::isAvailable($quote);
	}

}