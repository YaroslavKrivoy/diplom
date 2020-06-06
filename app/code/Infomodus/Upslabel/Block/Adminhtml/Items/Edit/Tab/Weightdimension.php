<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Weightdimension extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Weight and Dimension');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Weight and Dimension');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'item_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $confParams = $model['handy']->defConfParams;
        $fieldset = $form->addFieldset('weightdimension_fieldset', ['legend' => __('Weight and Dimension')]);
        $fieldset->addField(
            'weightunits',
            'select',
            ['name' => 'weightunits',
                'label' => __('Specific unit weight'),
                'title' => __('Specific unit weight'),
                'required' => true,
                'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Weight')->getArray(),
                'value' => $confParams['weightunits']
            ]
        );
        $fieldset->addField(
            'unitofmeasurement',
            'select',
            ['name' => 'unitofmeasurement',
                'label' => __('Unit of measurement'),
                'title' => __('Unit of measurement'),
                'required' => false,
                'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Unitofmeasurement')->getArray(),
                'value' => $confParams['unitofmeasurement']
            ]
        );

        /*$form->setValues($model->getData());*/
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
