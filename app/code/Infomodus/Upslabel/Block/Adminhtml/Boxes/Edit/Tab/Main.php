<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Boxes\Edit\Tab;


use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;


class Main extends Generic implements TabInterface
{
    /**
     * Main constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Box Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Box Information');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_boxes');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Box Information')]);
        if ($model->getId()) {
            $fieldset->addField('box_id', 'hidden', ['name' => 'box_id']);
        }

        $fieldset->addField('enable',
            'select',
            [
                'name' => 'enable',
                'label' => __('Enabled'),
                'required' => true,
                'options' => array(0 => __('No'), 1 => __('Yes'))
            ]);

        $fieldset->addField('name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'required' => true
            ]);

        $fieldset->addField('width',
            'text',
            [
                'name' => 'width',
                'label' => __('Width'),
                'required' => true
            ]);

        $fieldset->addField('outer_width',
            'text',
            [
                'name' => 'outer_width',
                'label' => __('Outer width'),
                'required' => true
            ]);

        $fieldset->addField('lengths',
            'text',
            [
                'name' => 'lengths',
                'label' => __('Length'),
                'required' => true
            ]);

        $fieldset->addField('outer_lengths',
            'text',
            [
                'name' => 'outer_lengths',
                'label' => __('Outer length'),
                'required' => true
            ]);

        $fieldset->addField('height',
            'text',
            [
                'name' => 'height',
                'label' => __('Height'),
                'required' => true
            ]);

        $fieldset->addField('outer_height',
            'text',
            [
                'name' => 'outer_height',
                'label' => __('Outer height'),
                'required' => true
            ]);

        $fieldset->addField('max_weight',
            'text',
            [
                'name' => 'max_weight',
                'label' => __('Max weight'),
                'required' => true
            ]);

        $fieldset->addField('empty_weight',
            'text',
            [
                'name' => 'empty_weight',
                'label' => __('Empty weight'),
                'required' => true
            ]);

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
