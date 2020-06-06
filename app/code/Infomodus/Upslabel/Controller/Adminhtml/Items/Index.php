<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Items;

class Index extends \Infomodus\Upslabel\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Infomodus_Upslabel::upslabel');
        $resultPage->getConfig()->getTitle()->prepend(__('UPS labels'));
        $resultPage->addBreadcrumb(__('UPS labels'), __('UPS labels'));
        $resultPage->addBreadcrumb(__('Labels'), __('Labels'));
        return $resultPage;
    }
}
