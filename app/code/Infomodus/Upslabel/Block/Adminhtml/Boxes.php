<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */
namespace Infomodus\Upslabel\Block\Adminhtml;

class Boxes extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'boxes';
        $this->_headerText = __('Boxes');
        $this->_addButtonLabel = __('Add New Box');
        parent::_construct();
    }
}
