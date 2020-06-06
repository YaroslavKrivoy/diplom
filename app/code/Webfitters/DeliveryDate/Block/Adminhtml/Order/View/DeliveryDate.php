<?php
namespace Webfitters\DeliveryDate\Block\Adminhtml\Order\View;

class DeliveryDate extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder {
	
	protected $customer;
	
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Model\Customer $customer,
        array $data = []
	){
		parent::__construct($context, $registry, $adminHelper, $data);
	}
	
	public function getDeliveryDate(){
		if($this->getOrder()->getDeliveryDate()){
			return \Carbon\Carbon::parse($this->getOrder()->getDeliveryDate())->format('m/d/Y');
		}
		return '';
	}
	
}