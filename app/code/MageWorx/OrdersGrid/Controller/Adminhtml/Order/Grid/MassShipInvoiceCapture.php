<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Controller\Adminhtml\Order\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassShipInvoiceCapture extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $redirectUrl = 'sales/order/index';

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var InvoiceOrderInterface
     */
    protected $invoiceOrder;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

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
     * @var ShipOrderInterface
     */
    protected $shipOrder;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param InvoiceOrderInterface $invoiceOrder
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Invoice $pdfInvoice
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param ShipOrderInterface $shipOrder
     */
    public function __construct(
        Context $context,
        Filter $filter,
        InvoiceOrderInterface $invoiceOrder,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Invoice $pdfInvoice,
        OrderCollectionFactory $orderCollectionFactory,
        ShipOrderInterface $shipOrder
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->invoiceOrder = $invoiceOrder;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
        $this->pdfInvoice = $pdfInvoice;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->shipOrder = $shipOrder;
        $this->backendSession = $context->getSession();
    }

    /**
     * Update is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $invoice = (bool)$this->_request->getParam('invoice');
        $capture = (bool)$this->_request->getParam('capture');
        $ship = (bool)$this->_request->getParam('ship');
        $sendMails = (bool)$this->_request->getParam('email');
        $print = (bool)$this->_request->getParam('print');

        $invoiced = 0;
        $shipped = 0;
        $invoiceErrors = 0;
        $shipmentErrors = 0;

        $collection = $this->filter->getCollection($this->orderCollectionFactory->create());
        foreach ($collection->getAllIds() as $entityId) {
            // Do invoice
            if ($invoice) {
                try {
                    $this->invoiceOrder->execute($entityId, $capture, [], $sendMails);
                    $invoiced++;
                } catch (\Exception $e) {
                    $invoiceErrors++;
                }
            }
            // Do shipment
            if ($ship) {
                try {
                    $this->shipOrder->execute($entityId, [], $sendMails);
                    $shipped++;
                } catch (\Exception $e) {
                    $shipmentErrors++;
                }
            }
        }

        /**
         * Add result message with counters
         * @var string $message
         */
        $message = __('Result: ');
        if ($invoice) {
            $message .= __('Successfully invoiced: %1 ', $invoiced);
            if ($invoiceErrors) {
                $this->messageManager->addErrorMessage(__('Not invoiced %1', $invoiceErrors));
            }
        }
        if ($ship) {
            $message .= __('Successfully shipped: %1 ', $shipped);
            if ($shipmentErrors) {
                $this->messageManager->addErrorMessage(__('Not shipped %1', $shipmentErrors));
            }
        }
        $this->messageManager->addSuccessMessage($message);

        if ($print) {
            $invoicesCollection = $this->invoiceCollectionFactory
                ->create()
                ->setOrderFilter(['in' => $collection->getAllIds()]);
            $shipmentCollection = $this->shipmentCollectionFactory
                ->create()
                ->setOrderFilter(['in' => $collection->getAllIds()]);

            if ($invoice && !$invoicesCollection->getSize() && $ship && !$shipmentCollection->getSize()) {
                $this->messageManager->addErrorMessage(
                    __('There are no printable documents related to selected orders.')
                );
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory
                    ->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath($this->redirectUrl);

                return $resultRedirect;
            }

            if ($invoice) {
                $this->backendSession->setPrintInvoicesIds(implode(',', $invoicesCollection->getAllIds()));
            } else {
                $this->backendSession->setPrintInvoicesIds(null);
            }
            if ($ship) {
                $this->backendSession->setPrintShipmentsIds(implode(',', $shipmentCollection->getAllIds()));
            } else {
                $this->backendSession->setPrintShipmentsIds(null);
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl, ['print_invoices' => 1]);

            return $resultRedirect;
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->redirectUrl);

        return $resultRedirect;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $invoice = (bool)$this->_request->getParam('invoice');
        $ship = (bool)$this->_request->getParam('ship');
        $result = true;
        if ($invoice) {
            $result = $result && $this->_authorization->isAllowed('MageWorx_OrdersGrid::invoice');
        }
        if ($ship) {
            $result = $result && $this->_authorization->isAllowed('MageWorx_OrdersGrid::ship');
        }

        return  $result;
    }
}
