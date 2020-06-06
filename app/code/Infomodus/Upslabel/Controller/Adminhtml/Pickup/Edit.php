<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Pickup;

class Edit extends \Infomodus\Upslabel\Controller\Adminhtml\Pickup
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Infomodus\Upslabel\Model\Pickup');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                $this->_redirect('infomodus_upslabel/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->coreRegistry->register('current_infomodus_upslabel_pickup', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('pickup_pickup_edit');
        $this->_view->renderLayout();
    }
}
