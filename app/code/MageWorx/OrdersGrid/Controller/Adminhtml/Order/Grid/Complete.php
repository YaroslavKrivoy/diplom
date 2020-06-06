<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Controller\Adminhtml\Order\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Exception\CouldNotInvoiceException;
use Magento\Sales\Exception\CouldNotShipException;
use Magento\Sales\Exception\DocumentValidationException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class Complete extends Action
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
     * @var InvoiceOrderInterface
     */
    protected $invoiceOrder;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ShipOrderInterface
     */
    protected $shipOrder;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param InvoiceOrderInterface $invoiceOrder
     * @param FileFactory $fileFactory
     * @param OrderCollectionFactory $collectionFactory
     * @param ShipOrderInterface $shipOrder
     */
    public function __construct(
        Context $context,
        Filter $filter,
        InvoiceOrderInterface $invoiceOrder,
        FileFactory $fileFactory,
        OrderCollectionFactory $collectionFactory,
        ShipOrderInterface $shipOrder
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->invoiceOrder = $invoiceOrder;
        $this->fileFactory = $fileFactory;
        $this->collectionFactory = $collectionFactory;
        $this->shipOrder = $shipOrder;
    }

    /**
     * Update is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $updatedCountInvoice = 0;
        $notUpdatedCountInvoice = 0;
        $updatedCountShip = 0;
        $notUpdatedCountShip = 0;

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            foreach ($collection->getAllIds() as $entityId) {
                try {
                    $this->invoiceOrder->execute($entityId);
                    $updatedCountInvoice++;
                } catch (DocumentValidationException $e) {
                    $notUpdatedCountInvoice++;
                } catch (CouldNotInvoiceException $e) {
                    $notUpdatedCountInvoice++;
                }
                try {
                    $this->shipOrder->execute($entityId);
                    $updatedCountShip++;
                } catch (DocumentValidationException $e) {
                    $notUpdatedCountShip++;
                } catch (CouldNotShipException $e) {
                    $notUpdatedCountShip++;
                }
            }

            $this->messageManager->addSuccessMessage(__('Successfully invoiced %1', $updatedCountInvoice))
                ->addSuccessMessage(__('Not invoiced %1', $notUpdatedCountInvoice))
                ->addSuccessMessage(__('Successfully shipped %1', $updatedCountShip))
                ->addSuccessMessage(__('Not shipped %1', $notUpdatedCountShip));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } finally {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);

            return $resultRedirect;
        }
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OrdersGrid::invoice') &&
        $this->_authorization->isAllowed('MageWorx_OrdersGrid::ship');
    }
}
