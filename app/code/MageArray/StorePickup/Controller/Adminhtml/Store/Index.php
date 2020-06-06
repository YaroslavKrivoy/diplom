<?php

namespace MageArray\StorePickup\Controller\Adminhtml\Store;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageArray_StorePickup::storepickup');
        $resultPage->addBreadcrumb(__('Store Pickup'), __('Attribute'));
        $resultPage->addBreadcrumb(__('Manage Store'), __('Manage Store'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Store'));

        return $resultPage;
    }
}
