<?php
namespace Webfitters\CustomerNote\Block\Adminhtml\Order\View;

class AccountNote extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder {
	
	protected $customer;
	
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Model\Customer $customer,
        array $data = []
	){
		$this->customer = $customer;
		parent::__construct($context, $registry, $adminHelper, $data);
	}
	
	public function getAccountNotes(){
		if ($this->getOrder()->getCustomerIsGuest() || !$this->getOrder()->getCustomerId()) {
            return 'N/A';
        }
		$customer = $this->customer->load($this->getOrder()->getCustomerId());
		return $customer->getCustomerNote();
	}
	
}