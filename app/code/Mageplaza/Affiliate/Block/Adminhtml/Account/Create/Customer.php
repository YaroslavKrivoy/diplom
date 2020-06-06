<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Adminhtml\Account\Create;

/**
 * Class Customer
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction\Create
 */
class Customer extends \Magento\Backend\Block\Widget
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('affiliate_account_customer');
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please select a customer');
    }
}
