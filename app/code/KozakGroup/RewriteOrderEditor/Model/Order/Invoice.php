<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 20.09.19
 * Time: 16:06
 */

namespace KozakGroup\RewriteOrderEditor\Model\Order;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order\Pdf\Invoice as InvoicePdf;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Controller\Adminhtml\Order\Invoice\Capture;

class Invoice
{
    protected $orderRepository;

    protected $_invoiceService;

    protected $_transactionFactory;

    protected $pdfInvoice;

    protected $fileFactory;

    protected $dateTime;

    protected $_objectManager;

    protected $_filesystem;

    protected $_capture;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        InvoicePdf $pdfInvoice,
        FileFactory $fileFactory,
        DateTime $dateTime,
        ObjectManagerInterface $_objectManager,
        \Magento\Framework\Filesystem $filesystem,
        Capture $_capture
    )
    {
        $this->orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transactionFactory = $transactionFactory;
        $this->pdfInvoice = $pdfInvoice;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->_objectManager = $_objectManager;
        $this->_filesystem = $filesystem;
        $this->_capture = $_capture;
    }

    public function createInvoice($order)
    {
        foreach ($order->getItems() as $item) {
            $item->setIsQtyDecimal(1);
        }
        $invoice = $this->_invoiceService->prepareInvoice($order);
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);

        if ($order->canInvoice()) {

            $invoice->register();

            $transaction = $this->_transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            $transaction->save();
            return $this->sendAndPrintInvoice($invoice);
        }
        else{
            $this->_capture->execute();

            //return $this->sendAndPrintInvoice($invoice);
        }
    }

    public function sendAndPrintInvoice($invoice)
    {
        $this->_objectManager->create('Magento\Sales\Model\Order\InvoiceNotifier')
            ->notify($invoice);
        $fileName = sprintf('invoice%s.pdf', $this->dateTime->date('Y-m-d_H-i-s'));
        $content = $this->pdfInvoice->getPdf(array($invoice))->render();
        $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::UPLOAD);
        $dir->writeFile($fileName, $content);
        return $fileName;
    }
}