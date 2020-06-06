<?php
namespace Webfitters\BulkInvoice\Controller\Adminhtml\Orders;

class Invoice extends \Magento\Backend\App\Action {

    protected $invoice;
    protected $order;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Sales\Model\Service\InvoiceService $invoice
    ) {
        parent::__construct($context);
        $this->invoice = $invoice;
        $this->order = $order;
    }

    public function execute() {
        $ids = $this->getRequest()->getParam('selected');
        $invoiced = 0;
        $total = count($ids);
        foreach($ids as $id){
            $order = $this->order->create()->load($id);
            if($order->canInvoice()){
                try {
                    $invoice = $this->invoice->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();
                    $invoiced++;
                } catch(\Exception $e){}
            }
        }
        $this->messageManager->addSuccessMessage(__('Successfully invoiced %1 of %2 orders', $invoiced, $total));
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('sales/order/index');
        return $redirect;
    }

}