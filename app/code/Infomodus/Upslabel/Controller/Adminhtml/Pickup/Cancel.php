<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pickup;

class Cancel extends \Infomodus\Upslabel\Controller\Adminhtml\Pickup
{
    protected $handy;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Helper\Handy $handy
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->handy = $handy;
    }

    public function execute()
    {
        $data=[];
        try {
            $id = $this->getRequest()->getParam('id');
            $model = $this->_objectManager->create('Infomodus\Upslabel\Model\Pickup')->load($id);
            $data = $model->getData();
            $response = $this->handy->cancelPickup($id);
            if (!isset($response['error'])) {
                $model->setData('pickup_cancel_request', $response['data']);
                $model->setData('pickup_cancel', $response['response']);
                $model->setData('status', "Canceled");
                $model->save();
                $this->messageManager->addSuccessMessage(__('Pickup was successfully canceled'));
                $this->_redirect('infomodus_upslabel/*/edit', ['id' => $model->getId()]);
                return;
            } else {
                $this->getResponse()
                    ->setContent($response['error']);
                return;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('infomodus_upslabel/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('infomodus_upslabel/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the item data. Please review the error log.')
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
            $this->_redirect('infomodus_upslabel/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('infomodus_upslabel/*/');
    }
}
