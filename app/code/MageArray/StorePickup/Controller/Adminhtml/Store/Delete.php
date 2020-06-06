<?php
namespace MageArray\StorePickup\Controller\Adminhtml\Store;

class Delete extends \MageArray\StorePickup\Controller\Adminhtml\Store
{
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('storepickup_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_storeFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager
                    ->addSuccess(__('Store has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['storepickup_id' => $id]
                );
            }
        }

        // display error message
        $this->messageManager
            ->addError(__('We can\'t find a store to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
