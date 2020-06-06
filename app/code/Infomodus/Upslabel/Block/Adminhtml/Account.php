<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */
namespace Infomodus\Upslabel\Block\Adminhtml;

class Account extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'account';
        $this->_headerText = __('Accounts');
        $this->_addButtonLabel = __('Add New Account');
        parent::_construct();
    }
}
