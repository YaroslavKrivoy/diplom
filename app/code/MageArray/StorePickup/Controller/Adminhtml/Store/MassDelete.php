<?php
namespace MageArray\StorePickup\Controller\Adminhtml\Store;

class MassDelete extends \MageArray\StorePickup\Controller\Adminhtml\Store
{
    public function execute()
    {
        $storeIds = $this->getRequest()->getParam('store');
        if (!is_array($storeIds) || empty($storeIds)) {
            $this->messageManager->addError(__('Please select store(s).'));
        } else {
            try {
                foreach ($storeIds as $storeId) {
                    $post = $this->_storeFactory->create()->load($storeId);
                    $post->delete();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
                        count($storeIds)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory
            ->create()->setPath('storepickup/*/index');
    }
}
