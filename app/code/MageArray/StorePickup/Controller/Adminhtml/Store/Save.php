<?php
namespace MageArray\StorePickup\Controller\Adminhtml\Store;

class Save extends \MageArray\StorePickup\Controller\Adminhtml\Store
{
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data['opening_days'] = implode(",", $data['opening_days']);
            $data['store_view_ids'] = implode(",", $data['store_view_ids']);
            $id = $this->getRequest()->getParam('storepickup_id');
            $model = $this->_storeFactory->create();

            if ($id) {
                $model->load($id);
            }

            try {
                $model->setData($data);
                if (isset($data['state_id'])) {
                    $model->setState($data['state_id']);
                }
                $model->save();

                $this->messageManager->addSuccess(__('Store has been saved.'));
                $this->backendSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        [
                            'storepickup_id' => $model->getId(),
                            '_current' => true
                        ]
                    );
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager
                    ->addException(
                        $e,
                        __('Something went wrong while saving store.')
                    );
            }

            $this->_getSession()->setFormData($data);
            $eavId = $this->getRequest()->getParam('storepickup_id');
            $this->_redirect(
                '*/*/edit',
                ['storepickup_id' => $eavId]
            );
            return;
        }

        $this->_redirect('*/*/');
    }
}
