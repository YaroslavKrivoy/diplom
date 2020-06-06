<?php
namespace Webfitters\Checkout\Plugin;

class SaveContainerFee {

	protected $request;

	public function __construct(
		\Magento\Framework\App\RequestInterface $request
	){
		$this->request = $request;
	}

	public function aroundSaveQuote(\Magento\Sales\Model\AdminOrder\Create $subject, \Closure $proceed){
		$quote = $subject->getQuote();
		$order = $this->request->getParam('order');
		if(strpos($this->request->getPathInfo(), 'sales/order_create/save') === FALSE) {
			if(isset($order['uses_container_fee']) && $order['uses_container_fee'] == 1){
				$quote->setUsesContainerFee($order['uses_container_fee']);
				$quote->setContainerFeeAdditive($order['container_fee_additive']);
				$quote->setContainerFee($order['container_fee']);
				$subject->setQuote($quote);
				$subject->collectShippingRates();
			} elseif(isset($order['uses_container_fee'])) {
				$quote->setUsesContainerFee(null);
				$quote->setContainerFee(null);
				$quote->setContainerFeeAdditive(null);
				$subject->setQuote($quote);
				$subject->collectShippingRates();
			}
		}
		return $proceed();
	}

}