<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Controller\Adminhtml\Form;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Backend\App\Action;
use Magento\Sales\Controller\Adminhtml\Order\Create;

class Search extends Create
{
    /**
     * @return $this
     */
    public function execute()
    {
        $updateResult = new DataObject();
        try {
            $resultPage = $this->resultPageFactory->create();
            $html = '';

            $optionsBlock = $resultPage->getLayout()->getBlock('search');
            if (!empty($optionsBlock)) {
                $html .= $optionsBlock->toHtml();
            }

            $createBlock = $resultPage->getLayout()->getBlock('create');
            if (!empty($createBlock)) {
                $html .= $createBlock->toHtml();
            }

            $updateResult->setSearchGrid($html);
            $updateResult->setOk(true);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $updateResult->setError(true);
            $updateResult->setMessage($errorMessage);
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($updateResult);
    }
}
