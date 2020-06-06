<?php
namespace MageArray\StorePickup\Controller\Adminhtml\Store;

class Edit extends \MageArray\StorePickup\Controller\Adminhtml\Store
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('storepickup_id');
        $model = $this->_storeFactory->create();

        if ($id) {
            $model->load($id);

            if (!$model->getStorepickupId()) {
                $this->messageManager
                    ->addError(__('This store no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('store_post', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
