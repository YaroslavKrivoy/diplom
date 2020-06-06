<?php
namespace Webfitters\Checkout\Block\Adminhtml\Invoice\Create;

class BillingMethod extends \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method {

	protected $request;
	protected $orderFactory;
	private $order;
	protected $invoiceFactory;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        array $data = []
	) {
		parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
		$this->request = $request;
		$this->orderFactory = $orderFactory;
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

}