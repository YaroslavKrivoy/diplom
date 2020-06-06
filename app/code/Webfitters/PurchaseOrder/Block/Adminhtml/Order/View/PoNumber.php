<?php
namespace Webfitters\PurchaseOrder\Block\Adminhtml\Order\View;

class PoNumber extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder {
	
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
	
	public function getPoNumber(){
		return $this->getOrder()->getPoNumber();
	}
	
}