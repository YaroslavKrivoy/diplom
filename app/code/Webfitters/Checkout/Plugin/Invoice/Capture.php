<?php
namespace Webfitters\Checkout\Plugin\Invoice;

class Capture {

    protected $url;
    protected $result;
    protected $redirect;
    protected $request;
    protected $messages;
    protected $order;
    protected $transaction;
    protected $payment;
    protected $quote;
    protected $invoice;

    public function __construct(
		\Magento\Framework\UrlInterface $url,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messages,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order\Payment\Transaction $transaction,
        \Magento\Quote\Model\Quote\PaymentFactory $payment,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory
    ) {
		$this->url = $url;
		$this->result = $result;
		$this->redirect = $redirect;
		$this->request = $request;
		$this->messages = $messages;
        $this->transaction = $transaction;
        $this->payment = $payment;
        $this->quote = $quoteFactory;
        $this->invoice = $invoiceFactory->create()->load($this->request->getParam('invoice_id'));
		$this->order = $orderFactory->create()->load($this->invoice->getOrderId());
    }

    public function aroundExecute(\Magento\Sales\Controller\Adminhtml\Order\Invoice\Capture $subject, $proceed) {
        $invoice = $this->request->getParam('invoice');
    	if($this->order->getPayment()->getMethod() == 'webfitters_paylater'){
            try {
                if(!isset($this->request->getParam('payment')['method'])){
                    throw new \Exception('Payment method is required.');
                }
                $data = $this->request->getParam('payment');
                $data['customer_id'] = $this->order->getCustomerId();
                $payment = $this->order->getPayment();
                $payment->setMethod($this->request->getParam('payment')['method']);
                $payment->setLastTransId(null);
                $payment->setTransactionId(null);
                $payment->setAdditionalInformation($data);
                $transaction = $this->transaction->setPayment($payment)
                ->setOrder($this->order)
                ->setTransactionId(null)
                ->setAdditionalInformation(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $data)
                ->setFailSafe(true);
                $payment->setParentTransactionId(null);
                $payment->save();
                if($this->request->getParam('payment')['method'] != 'purchaseorder'){
                    $payment->authorize($payment, $this->order->getGrandTotal());
                }
                $this->order->save();
                $transaction->save();
            } catch(\Exception $e) {
                $payment = $this->order->getPayment();
                $payment->setMethod('webfitters_paylater');
                $payment->save();
                $this->messages->addError($e->getMessage());
                $result = $this->result->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                $result->setUrl($this->redirect->getRefererUrl());
                return $result;
            }
    	}
        return $proceed();
    }

}