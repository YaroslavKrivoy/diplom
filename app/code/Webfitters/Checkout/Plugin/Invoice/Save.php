<?php
namespace Webfitters\Checkout\Plugin\Invoice;

/*class Save {

    protected $url;
    protected $result;
    protected $redirect;
    protected $request;
    protected $messages;
    protected $order;
    protected $transaction;
    protected $payment;
    protected $quote;

    public function __construct(
		\Magento\Framework\UrlInterface $url,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messages,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order\Payment\Transaction $transaction,
        \Magento\Quote\Model\Quote\PaymentFactory $payment
    ) {
		$this->url = $url;
		$this->result = $result;
		$this->redirect = $redirect;
		$this->request = $request;
		$this->messages = $messages;
        $this->transaction = $transaction;
        $this->payment = $payment;
        $this->quote = $quoteFactory;
		$this->order = $orderFactory->create()->load($this->request->getParam('order_id'));
    }

    public function aroundExecute(\Magento\Sales\Controller\Adminhtml\Order\Invoice\Save $subject, $proceed) {
        $return = $proceed();
        $invoice = $this->request->getParam('invoice');
    	if($this->order->getPayment()->getMethod() == 'webfitters_paylater' && $invoice['capture_case'] != 'not_capture'){
            try {
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
                    $payment->capture($payment, $this->order->getGrandTotal());
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
        return $return;
    }

}*/

/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;*/

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_invoice';

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var InvoiceService
     */
    private $invoiceService;
    protected $url;
    protected $result;
    protected $redirect;
    protected $request;
    protected $messages;
    protected $order;
    protected $transaction;
    protected $payment;
    protected $quote;
    /**
     * @param Action\Context $context
     * @param Registry $registry
     * @param InvoiceSender $invoiceSender
     * @param ShipmentSender $shipmentSender
     * @param ShipmentFactory $shipmentFactory
     * @param InvoiceService $invoiceService
     */
    public function __construct(
        Action\Context $context,
        Registry $registry,
        InvoiceSender $invoiceSender,
        ShipmentSender $shipmentSender,
        ShipmentFactory $shipmentFactory,
        InvoiceService $invoiceService,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messages,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order\Payment\Transaction $transaction,
        \Magento\Quote\Model\Quote\PaymentFactory $payment
    ) {
        $this->registry = $registry;
        $this->invoiceSender = $invoiceSender;
        $this->shipmentSender = $shipmentSender;
        $this->shipmentFactory = $shipmentFactory;
        $this->invoiceService = $invoiceService;
        $this->url = $url;
        $this->result = $result;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->messages = $messages;
        $this->transaction = $transaction;
        $this->payment = $payment;
        $this->quote = $quoteFactory;
        $this->order = $orderFactory->create()->load($this->request->getParam('order_id'));
        parent::__construct($context);
    }

    /**
     * Prepare shipment
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function _prepareShipment($invoice)
    {
        $invoiceData = $this->getRequest()->getParam('invoice');

        $shipment = $this->shipmentFactory->create(
            $invoice->getOrder(),
            isset($invoiceData['items']) ? $invoiceData['items'] : [],
            $this->getRequest()->getPost('tracking')
        );

        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }

    /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     *
     * @return \Magento\Framework\Controller\ResultInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            return $resultRedirect->setPath('sales/order/index');
        }

        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setCommentText($data['comment_text']);
        }

        try {
            $invoiceData = $this->getRequest()->getParam('invoice', []);
            $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            if (!$invoice) {
                throw new LocalizedException(__('We can\'t save the invoice right now.'));
            }

            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $this->registry->register('current_invoice', $invoice);
            if (!empty($data['capture_case'])) {
                if($order->getPayment()->getMethod() == 'webfitters_paylater' && $data['capture_case'] != 'not_capture'){
                    try {
                        $p = $this->request->getParam('payment');
                        $p['customer_id'] = $order->getCustomerId();
                        $payment = $order->getPayment();
                        $payment->setMethod($this->request->getParam('payment')['method']);
                        $payment->setLastTransId(null);
                        $payment->setTransactionId(null);
                        $payment->setAdditionalInformation($p);
                        $transaction = $this->transaction->setPayment($payment)
                        ->setOrder($order)
                        ->setTransactionId(null)
                        ->setAdditionalInformation(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $p)
                        ->setFailSafe(true);
                        $payment->setParentTransactionId(null);
                        $payment->save();
                        if($this->request->getParam('payment')['method'] != 'purchaseorder'){
                            $payment->authorize($payment, $order->getGrandTotal());
                        }
                        $order->save();
                        $transaction->save();
                    } catch(\Exception $e) {
                        $payment = $order->getPayment();
                        $payment->setMethod('webfitters_paylater');
                        $payment->save();
                        $this->messages->addError($e->getMessage());
                        $result = $this->result->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                        $result->setUrl($this->redirect->getRefererUrl());
                        return $result;
                    }
                }


                $invoice->setRequestedCaptureCase($data['capture_case']);
            }

            if (!empty($data['comment_text'])) {
                $invoice->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $invoice->setCustomerNote($data['comment_text']);
                $invoice->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }

            $invoice->register();

            $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = $this->_objectManager->create(
                \Magento\Framework\DB\Transaction::class
            )->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $shipment = false;
            if (!empty($data['do_shipment']) || (int)$invoice->getOrder()->getForcedShipmentWithInvoice()) {
                $shipment = $this->_prepareShipment($invoice);
                if ($shipment) {
                    $transactionSave->addObject($shipment);
                }
            }
            $transactionSave->save();

            if (!empty($data['do_shipment'])) {
                $this->messageManager->addSuccess(__('You created the invoice and shipment.'));
            } else {
                $this->messageManager->addSuccess(__('The invoice has been created.'));
            }

            // send invoice/shipment emails
            try {
                if (!empty($data['send_email'])) {
                    $this->invoiceSender->send($invoice);
                }
            } catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
            }
            if ($shipment) {
                try {
                    if (!empty($data['send_email'])) {
                        $this->shipmentSender->send($shipment);
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                    $this->messageManager->addError(__('We can\'t send the shipment right now.'));
                }
            }
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        return $resultRedirect->setPath('sales/*/new', ['order_id' => $orderId]);
    }
}
