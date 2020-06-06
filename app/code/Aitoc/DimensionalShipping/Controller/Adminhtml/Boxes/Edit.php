<?php

namespace Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;

class Edit extends \Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes
{

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $boxId = $this->getRequest()->getParam('id');
        if ($boxId) {
            $model = $this->boxRepository->get($boxId);
            /** TODO: change model to resource model */
            if (!$model->getItemId()) {
                $this->messageManager->addErrorMessage(__('This Supplier no longer exists.'));

                return $this->_redirect('aitdimensionalshipping/*/');
            }
        } else {
            $model = $this->boxFactory->create();
        }
        // set entered data if was error when we do save
        $data = $this->_session->getSupplierData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('supplier');
        if (!empty($attributeData) && $boxId === null) {
            $model->addData($attributeData);
        }
        $this->coreRegistry->register('entity_box', $model);
        $item = __('Dimensional Shipping: New Box');
        if ($boxId) {
            $item = __('Dimensional Shipping: Edit Box');
        }
        $title      = $item->getText() . ($boxId ? " '" . $model->getName() . "'" : "");
        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
