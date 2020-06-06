<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;

use Aitoc\DimensionalShipping\Controller\Adminhtml\Boxes;

class Delete extends Boxes
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
        $redirectResult = $this->resultRedirectFactory->create();
        if ($boxId = $this->getRequest()->getParam('id')) {
            try {
                $boxModel = $this->boxRepository->get($boxId);
                $this->boxRepository->delete($boxModel);
                $this->messageManager->addSuccess(__('The box has been deleted.'));

                $redirectResult->setPath('aitdimensionalshipping/*/');

                return $redirectResult;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while deleting. Please review the error log.')
                );
            }
            $redirectResult->setUrl($this->_redirect->getRedirectUrl($this->getUrl('*')));

            return $redirectResult;
        }
        $redirectResult->setPath('aitdimensionalshipping/*/');

        return $redirectResult;

    }
}
