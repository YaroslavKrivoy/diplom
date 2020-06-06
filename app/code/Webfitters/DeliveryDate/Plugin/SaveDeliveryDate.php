<?php
namespace Webfitters\DeliveryDate\Plugin;

class SaveDeliveryDate {

	protected $request;

	public function __construct(
		\Magento\Framework\App\RequestInterface $request
	){
		$this->request = $request;
	}

	public function aroundSaveQuote(\Magento\Sales\Model\AdminOrder\Create $subject, \Closure $proceed){
		$quote = $subject->getQuote();
		$order = $this->request->getParam('order');
		if(isset($order['delivery_date'])){
			if($order['delivery_date'] != ''){
				$quote->setDeliveryDate(\Carbon\Carbon::parse($order['delivery_date'])->toDateTimeString());
			} else {
				$quote->setDeliveryDate(null);
			}
		}
		$subject->setQuote($quote);
		return $proceed();
	}

}