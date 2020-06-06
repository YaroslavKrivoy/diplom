<?php
namespace MageArray\StorePickup\Block\Adminhtml;

class Store extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_store';
        $this->_blockGroup = 'MageArray_StorePickup';
        $this->_headerText = __('Manage Store');
        $this->_addButtonLabel = __('Add New Store');
        parent::_construct();
    }
}
