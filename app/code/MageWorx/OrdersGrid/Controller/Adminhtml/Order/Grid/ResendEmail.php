<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Controller\Adminhtml\Order\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\ShipmentManagementInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class ResendEmail extends Action
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
    protected $collectionFactory;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var ShipmentManagementInterface
     */
    protected $shipmentManagement;

    /**
     * @var InvoiceManagementInterface
     */
    protected $invoiceManagement;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param OrderCollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     * @param ShipmentManagementInterface $shipmentManagement
     * @param InvoiceManagementInterface $invoiceManagement
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        ShipmentManagementInterface $shipmentManagement,
        InvoiceManagementInterface $invoiceManagement,
        InvoiceRepositoryInterface $invoiceRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        $this->shipmentManagement = $shipmentManagement;
        $this->invoiceManagement = $invoiceManagement;
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Update is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $orderFlag = (bool)$this->_request->getParam('order');
        $invoiceFlag = (bool)$this->_request->getParam('invoice');
        $shipmentFlag = (bool)$this->_request->getParam('shipment');

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection->getAllIds() as $entityId) {
            if ($orderFlag) {
                $this->resendOrderEmails($entityId);
            }
            if ($invoiceFlag) {
                $this->resendInvoiceEmails($entityId);
            }
            if ($shipmentFlag) {
                $this->resendShipmentEmails($entityId);
            }
        }

        if ($orderFlag) {
            $this->messageManager->addSuccessMessage(__('The order emails have been sent.'));
        }
        if ($invoiceFlag) {
            $this->messageManager->addSuccessMessage(__('The invoice emails have been sent.'));
        }
        if ($shipmentFlag) {
            $this->messageManager->addSuccessMessage(__('The shipment emails have been sent.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->redirectUrl);

        return $resultRedirect;
    }

    /**
     * Notify customer (New order email)
     *
     * @param $orderId
     * @return bool
     */
    protected function resendOrderEmails($orderId)
    {
        try {
            return $this->orderManagement->notify($orderId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Resend invoices emails of corresponding order
     * Returns false in case of error and does not sends any email
     *
     * @param $orderId
     * @return bool
     */
    protected function resendInvoiceEmails($orderId)
    {
        try {
            /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->addFilter(InvoiceInterface::ORDER_ID, $orderId)->create();
            $invoices = $this->invoiceRepository->getList($searchCriteria)->getItems();
        } catch (\Exception $e) {
            return false;
        }

        foreach ($invoices as $invoice) {
            $this->invoiceManagement->notify($invoice->getEntityId());
        }

        return true;
    }

    /**
     * Resend shipment emails of corresponding order
     * Returns false in case of error and does not sends any email
     *
     * @param $orderId
     * @return bool
     */
    protected function resendShipmentEmails($orderId)
    {
        try {
            /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->addFilter(ShipmentInterface::ORDER_ID, $orderId)->create();
            $shipments = $this->shipmentRepository->getList($searchCriteria)->getItems();
        } catch (\Exception $e) {
            return false;
        }

        foreach ($shipments as $shipment) {
            $this->shipmentManagement->notify($shipment->getEntityId());
        }

        return true;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OrdersGrid::resend_emails');
    }
}
