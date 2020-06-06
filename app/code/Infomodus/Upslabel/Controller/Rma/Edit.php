<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Infomodus\Upslabel\Controller\Rma;

class Edit extends \Infomodus\Upslabel\Controller\Rma
{
    /**
     * Customer address edit action
     *
     * @return \Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        $resultPage = $this->getResultPageFactory()->create();
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/order/history');
        }
        return $resultPage;
    }
}
