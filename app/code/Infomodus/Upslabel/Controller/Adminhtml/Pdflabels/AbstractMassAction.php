<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Controller\Adminhtml\Pdflabels;

use Infomodus\Upslabel\Controller\Adminhtml\Pdflabels;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class AbstractMassStatus
 */
abstract class AbstractMassAction extends Pdflabels
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/';

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = null;
            switch ($this->getRequest()->getParam('namespace')) {
                case 'sales_order_grid':
                    $collection = $this->filter->getCollection($this->collectionFactory->create());
                    break;
                case 'sales_order_shipment_grid':
                    $collection = $this->filter->getCollection($this->shipmentCollectionFactory->create());
                    break;
                case 'sales_order_creditmemo_grid':
                    $collection = $this->filter->getCollection($this->creditmemoCollectionFactory->create());
                    break;
                default:
                    $collection = $this->labelCollectionFactory->create()
                        ->addFieldToFilter('upslabel_id', ['in' => $this->getRequest()->getParam('upslabel_id')]);
                    break;
            }
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'infomodus_upslabel/*/';
    }

    /**
     * Set status to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract
    protected function massAction(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection);
}
