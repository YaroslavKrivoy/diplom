<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Controller\Adminhtml\Order\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;

class PrintPdf extends Action
{
    /**
     * @var string
     */
    protected $redirectUrl = 'sales/order/index';

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Invoice
     */
    protected $pdfInvoice;

    /**
     * @var Shipment
     */
    protected $pdfShipment;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @param Context $context
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Invoice $pdfInvoice
     * @param Shipment $pdfShipment
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Invoice $pdfInvoice,
        Shipment $pdfShipment,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->pdfInvoice = $pdfInvoice;
        $this->backendSession = $context->getSession();
        $this->resultJsonFactory = $resultJsonFactory;
        $this->pdfShipment = $pdfShipment;
    }

    /**
     * Update is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $isCheck = $this->getRequest()->getParam('check');
        $invoiceIds = explode(',', $this->backendSession->getPrintInvoicesIds());
        $shipmentsIds = explode(',', $this->backendSession->getPrintShipmentsIds());
        $invoicesCollection = $this->invoiceCollectionFactory
            ->create()
            ->addFieldToFilter('entity_id', ['in' => $invoiceIds]);
        $shipmentsCollection = $this->shipmentCollectionFactory
            ->create()
            ->addFieldToFilter('entity_id', ['in' => $shipmentsIds]);

        if ($isCheck) {
            /** @var \Magento\Framework\Controller\Result\Json $result */
            $result = $this->resultJsonFactory->create();
            if (!$invoicesCollection->getSize() && !$shipmentsCollection->getSize()) {
                return $result->setData(['success' => false]);
            } else {
                return $result->setData(['success' => true]);
            }
        } else {
            $this->backendSession->setPrintInvoicesIds(null);
            $this->backendSession->setPrintShipmentsIds(null);

            $pdf = new \Zend_Pdf();
            if ($invoicesCollection->getSize()) {
                $invoicePdf = $this->pdfInvoice->getPdf($invoicesCollection->getItems());
                $pdf->pages = array_merge($pdf->pages, $invoicePdf->pages);
            }

            if ($shipmentsCollection->getSize()) {
                $shipmentPdf = $this->pdfShipment->getPdf($shipmentsCollection->getItems());
                $pdf->pages = array_merge($pdf->pages, $shipmentPdf->pages);
            }

            return $this->fileFactory->create(
                sprintf('printed%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                $pdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OrdersGrid::invoice');
    }
}
