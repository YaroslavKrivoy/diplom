<?php
namespace Webfitters\Checkout\Block\Adminhtml\Invoice\Create;

class BillingMethodForm extends \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method\Form {

	protected $orderFactory;
	protected $quoteFactory;
    protected $invoiceFactory;
	protected $request;
	private $order;
	private $quote;
	private $paymentMethodList;
	private $paymentMethodInstanceFactory;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        array $data = [],
        array $additionalChecks = []
	) {
		parent::__construct($context, $paymentHelper, $methodSpecificationFactory, $sessionQuote, $data, $additionalChecks);
		$this->orderFactory = $orderFactory;
		$this->quoteFactory = $quoteFactory;
		$this->request = $request;
        $this->invoiceFactory = $invoiceFactory;
	}

	public function getOrder(){
		if(!$this->order){
            if($this->request->getParam('order_id')){
    			$this->order = $this->orderFactory->create()->load($this->request->getParam('order_id'));
            } else if($this->request->getParam('invoice_id')){
                $invoice = $this->invoiceFactory->create()->load($this->request->getParam('invoice_id'));
                $this->order = $this->orderFactory->create()->load($invoice->getOrderId());
            }
		}
		return $this->order;
	}

    public function isCapturingInvoice(){
        return $this->request->getParam('invoice_id')?true:false;
    }

	public function getQuote() {
        if(!$this->quote){
			$this->quote = $this->quoteFactory->create()->load($this->getOrder()->getQuoteId());
		}
		return $this->quote;
    }

	public function getMethods() {
        $methods = $this->getData('methods');
        if ($methods === null) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = [];
            foreach ($this->getPaymentMethodList()->getActiveList($store) as $method) {
                $methodInstance = $this->getPaymentMethodInstanceFactory()->create($method);
                if ($methodInstance->isAvailable($quote) && $this->_canUseMethod($methodInstance)) {
                    $this->_assignMethod($methodInstance);
                    $methods[] = $methodInstance;
                }
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }

    private function getPaymentMethodList(){
    	if ($this->paymentMethodList === null) {
            $this->paymentMethodList = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Payment\Api\PaymentMethodListInterface::class
            );
        }
        return $this->paymentMethodList;
    }

    private function getPaymentMethodInstanceFactory() {
        if ($this->paymentMethodInstanceFactory === null) {
            $this->paymentMethodInstanceFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Payment\Model\Method\InstanceFactory::class
            );
        }
        return $this->paymentMethodInstanceFactory;
    }

}