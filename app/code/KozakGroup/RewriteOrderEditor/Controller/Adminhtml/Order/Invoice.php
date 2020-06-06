<?php

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use KozakGroup\RewriteOrderEditor\Model\Order\Invoice as InvoiceModel;

class Invoice extends Action
{

    protected $orderRepository;

    protected $invoice;

    public $_storeManager;

    /**
     * Invoice constructor.
     * @param Action\Context $context
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param InvoiceModel $invoice
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        InvoiceModel $invoice,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->invoice = $invoice;
        $this->_storeManager = $storeManager;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id', 0);
        if ($orderId <= 0) {
            echo 'Order not found.';
            return;
        }
        try {
            // load order from database
            $order = $this->orderRepository->get($orderId);
            if ($order == null) {
                echo "Order not loaded from database.";
                return;
            }
            $invoicePdfFilename = $this->invoice->createInvoice($order);
            echo $this->_storeManager->getStore()->getBaseUrl(). 'pub/media/upload/' . $invoicePdfFilename;
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
        }
    }
}