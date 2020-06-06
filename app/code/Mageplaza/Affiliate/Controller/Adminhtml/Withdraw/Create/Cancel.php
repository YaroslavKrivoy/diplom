<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Adminhtml\Withdraw\Create;

/**
 * Class Cancel
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction\Create
 */
class Cancel extends \Magento\Backend\App\Action
{
    /**
     * Cancel transaction create
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $this->_getSession()->unsetData('withdraw_customer_id');
        $resultRedirect->setPath('affiliate/withdraw/create');

        return $resultRedirect;
    }
}
