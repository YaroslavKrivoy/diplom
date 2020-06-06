<?php
namespace Webfitters\Checkout\Block\Adminhtml\Order\Invoice;

class View extends \Magento\Sales\Block\Adminhtml\Order\Invoice\View {

	protected function _construct(){
		parent::_construct();
        if($this->getInvoice()->getOrder()->getPayment()->getMethod() == 'webfitters_paylater'){
    		$this->buttonList->remove('capture');
    		if ($this->_isAllowedAction('Magento_Sales::capture') && $this->getInvoice()->canCapture() && !$this->_isPaymentReview()) {
                $this->buttonList->add('capture', [
                    'label' => __('Capture'),
                    'title' => $this->getCaptureUrl(),
                    'class' => 'capture-post'
                ]);
            }
        }
	}

}