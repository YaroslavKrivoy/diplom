<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

namespace Infomodus\Upslabel\Controller\Adminhtml\Address;

class Index extends \Infomodus\Upslabel\Controller\Adminhtml\Address
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
        $resultPage->getConfig()->getTitle()->prepend(__('UPS: Address'));
        $resultPage->addBreadcrumb(__('Infomodus'), __('Infomodus'));
        $resultPage->addBreadcrumb(__('Address'), __('Address'));
        return $resultPage;
    }
}
