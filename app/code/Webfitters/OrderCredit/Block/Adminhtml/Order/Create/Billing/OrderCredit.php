<?php
namespace Webfitters\OrderCredit\Block\Adminhtml\Order\Create\Billing;

class OrderCredit extends \Magento\Backend\Block\Template {

	protected $order;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Sales\Model\AdminOrder\Create $order,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->order = $order;
	}

	public function getOrderCredit(){
		return number_format($this->order->getQuote()->getCreditAmount(), 2);
	}

}