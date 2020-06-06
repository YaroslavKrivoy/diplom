<?php
namespace Webfitters\DeliveryDate\Block\Adminhtml\Order\Create\Billing;

class DeliveryDate extends \Magento\Backend\Block\Template {

	protected $order;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context, 
		\Magento\Sales\Model\AdminOrder\Create $order,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->order = $order;
	}

	public function getDeliveryDate(){
		if($this->order->getQuote()->getDeliveryDate()){
			return \Carbon\Carbon::parse($this->order->getQuote()->getDeliveryDate())->format('m/d/Y');
		}
		return '';
	}

}