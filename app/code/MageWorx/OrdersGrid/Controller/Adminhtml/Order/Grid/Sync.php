<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Controller\Adminhtml\Order\Grid;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\CollectionFactory as OrderGridCustomCollectionFactory;

class Sync extends Action
{
    /**
     * @var OrderGridCustomCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param OrderGridCustomCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        OrderGridCustomCollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Sync orders data with our table
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        try {
            /** @var \MageWorx\OrdersGrid\Model\ResourceModel\Order\Grid\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->syncOrdersData();

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['success' => true, 'time' => time()]);

            return $resultJson;
        } catch (\Exception $e) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['success' => false, 'time' => time()]);

            return $resultJson;
        }
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_OrdersGrid::sync');
    }
}
