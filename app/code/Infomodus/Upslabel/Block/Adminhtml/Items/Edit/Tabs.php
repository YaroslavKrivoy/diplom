<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */
namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('infomodus_upslabel_items_edit_tabs');
        $this->setDestElementId('edit_form');
        switch ($this->getRequest()->getParam('direction')) {
            case 'refund':
                $label = 'RMA(return) UPS label';
                break;
            case 'invert':
                $label = 'Invert UPS label';
                break;
            default:
                $label = 'Shipping UPS label';
        }
        $this->setTitle(__($label));
    }
}
