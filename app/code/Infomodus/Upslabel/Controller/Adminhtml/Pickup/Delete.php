<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pickup;

class Delete extends \Infomodus\Upslabel\Controller\Adminhtml\Pickup
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
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $response = $this->handy->cancelPickup($id);
                if (!isset($response['error'])) {
                    $model = $this->_objectManager->create('Infomodus\Upslabel\Model\Pickup');
                    $model->load($id);
                    $model->delete();
                } else {
                    $this->getResponse()
                        ->setContent($response['error']);
                    return;
                }
                $this->messageManager->addSuccessMessage(__('You deleted the pickup.'));
                $this->_redirect('infomodus_upslabel/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete pickup right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('infomodus_upslabel/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a item to delete.'));
        $this->_redirect('infomodus_upslabel/*/');
    }
}
