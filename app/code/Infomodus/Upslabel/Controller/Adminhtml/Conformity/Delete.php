<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Conformity;

class Delete extends \Infomodus\Upslabel\Controller\Adminhtml\Conformity
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Infomodus\Upslabel\Model\Conformity');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the item.'));
                $this->_redirect('infomodus_upslabel/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
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
