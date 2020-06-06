<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Items\Editone\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Main extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Order');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('First step');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Select order')]);
        $fieldset->addField(
            'direction',
            'select',
            [
                'name' => 'direction',
                'label' => __('Direction'),
                'title' => __('Direction'),
                'required' => true,
                'options' => ['shipment' => 'Store -> Customer', 'invert' => 'Customer -> Store', 'refund' => 'RMA (return)']
            ]
        );
        $fieldset->addField(
            'order_id',
            'text',
            [
                'name' => 'order_id',
                'label' => __('Order #'),
                'title' => __('Order #'),
                'required' => true
            ]
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
