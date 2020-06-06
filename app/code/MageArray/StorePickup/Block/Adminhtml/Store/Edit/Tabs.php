<?php
namespace MageArray\StorePickup\Block\Adminhtml\Store\Edit;

/**
 * Class Tabs
 * @package MageArray\StorePickup\Block\Adminhtml\Store\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('store_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Information'));
    }

    /**
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Store Information'),
                'title' => __('Store Information'),
                'content' => $this->getLayout()
                    ->createBlock('MageArray\StorePickup\Block\Adminhtml\Store\Edit\Tab\Main')
                    ->toHtml(),
                'active' => true
            ]
        );
    }
}
