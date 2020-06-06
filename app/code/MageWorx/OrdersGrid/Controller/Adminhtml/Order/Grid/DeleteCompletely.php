<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Controller\Adminhtml\Order\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class DeleteCompletely extends Action
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
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\CollectionFactory
     */
    protected $additionalDataCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param OrderCollectionFactory $collectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param \MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\CollectionFactory $additionalDataCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        OrderRepositoryInterface $orderRepository,
        \MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\CollectionFactory $additionalDataCollectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        $this->additionalDataCollectionFactory = $additionalDataCollectionFactory;
    }

    /**
     * Update is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $ordersDeleted = 0;
        $errors = 0;
        try {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $orderIds = $collection->getAllIds();

            // Delete records from our custom table
            /** @var \MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\Collection $additionalDataCollection */
            $additionalDataCollection = $this->additionalDataCollectionFactory->create();
            $additionalDataCollection->deleteData($orderIds);

            // Delete regular orders
            /** @var \Magento\Sales\Model\Order $order */
            foreach ($collection as $order) {
                $order->getInvoiceCollection()->walk('delete');
                $order->getShipmentsCollection()->walk('delete');
                $order->getCreditmemosCollection()->walk('delete');
                $result = $this->orderRepository->delete($order);
                if ($result) {
                    $ordersDeleted++;
                } else {
                    $errors++;
                }
            }
            $this->messageManager->addSuccessMessage(
                __(
                    'Successfully deleted: %1 , 
                    Errors: %2',
                    $ordersDeleted,
                    $errors
                )
            );

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);

            return $resultRedirect;
        } catch (\Exception $e) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OrdersGrid::delete');
    }
}
