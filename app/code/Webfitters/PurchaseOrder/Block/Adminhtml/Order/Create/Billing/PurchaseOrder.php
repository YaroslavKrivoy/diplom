<?php
namespace Webfitters\PurchaseOrder\Block\Adminhtml\Order\Create\Billing;

class PurchaseOrder extends \Magento\Backend\Block\Template {

	protected $order;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Sales\Model\AdminOrder\Create $order,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->order = $order;
	}

	public function getPoNumber(){
		return $this->order->getQuote()->getPoNumber();
	}

}