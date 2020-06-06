<?php
namespace Webfitters\PurchaseOrder\Plugin;

class SavePoNumber {

	protected $request;

	public function __construct(
		\Magento\Framework\App\RequestInterface $request
	){
		$this->request = $request;
	}

	public function aroundSaveQuote(\Magento\Sales\Model\AdminOrder\Create $subject, \Closure $proceed){
		$quote = $subject->getQuote();
		$order = $this->request->getParam('order');
		if(isset($order['po_number'])){
			$quote->setPoNumber($order['po_number']);
		}
		$subject->setQuote($quote);
		return $proceed();
	}

}