<?php
namespace Mageplaza\Affiliate\Block\Adminhtml\Order\Create;

class Affiliate extends \Magento\Backend\Block\Template {

	protected $affiliate;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Mageplaza\Affiliate\Model\AccountFactory $affiliate,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->affiliate = $affiliate;
	}

	public function getAffiliates(){
		$affiliates = [];
		$start = $this->affiliate->create()->getCollection()->addFieldToFilter('group_id', ['neq' => 1])->addFieldToFilter('status', '1')->load();
		foreach($start as $aff){
			$affiliates[$aff->getId()] = ($aff->getCustomer()->getCompany())?$aff->getCustomer()->getCompany():$aff->getCustomer()->getFirstname().' '.$aff->getCustomer()->getLastname();
		}
		asort($affiliates);
		return $affiliates;
	}
	
}