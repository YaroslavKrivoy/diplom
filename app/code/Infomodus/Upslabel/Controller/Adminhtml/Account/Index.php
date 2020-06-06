<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Account;

class Index extends \Infomodus\Upslabel\Controller\Adminhtml\Account
{
    /**
     * Account list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Infomodus_Upslabel::upslabel');
        $resultPage->getConfig()->getTitle()->prepend(__('UPS: Third Party Shippers'));
        $resultPage->addBreadcrumb(__('Infomodus'), __('Infomodus'));
        $resultPage->addBreadcrumb(__('Accounts'), __('Accounts'));
        return $resultPage;
    }
}
