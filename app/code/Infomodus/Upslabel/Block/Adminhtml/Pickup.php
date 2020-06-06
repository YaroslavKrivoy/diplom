<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */
namespace Infomodus\Upslabel\Block\Adminhtml;

class Pickup extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'pickup';
        $this->_headerText = __('Pickup');
        $this->_addButtonLabel = __('Add New Pickup');
        parent::_construct();
    }
}
