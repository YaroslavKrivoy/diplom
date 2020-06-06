<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Address\Edit\Tab;


use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;


class Main extends Generic implements TabInterface
{
    private $_countries;

    /**
     * Main constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Country $countries
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Country $countries,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->_countries = $countries;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Address Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Address Information');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_address');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Address Information')]);
        if ($model->getId()) {
            $fieldset->addField('address_id', 'hidden', ['name' => 'address_id']);
        }

        $fieldset->addField('name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField('company',
            'text',
            [
                'name' => 'company',
                'label' => __('Company'),
                'required' => true
            ]
        );

        $fieldset->addField('attention',
            'text',
            [
                'name' => 'attention',
                'label' => __('Attention'),
                'required' => true
            ]
        );

        $fieldset->addField('phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone Number'),
                'required' => true
            ]
        );

        $fieldset->addField('street_one',
            'text',
            [
                'name' => 'street_one',
                'label' => __('Address Line 1'),
                'required' => true
            ]
        );

        $fieldset->addField('street_two',
            'text',
            [
                'name' => 'street_two',
                'label' => __('Address Line 2'),
            ]
        );

        $fieldset->addField('room',
            'text',
            [
                'name' => 'room',
                'label' => __('Room'),
            ]
        );

        $fieldset->addField('floor',
            'text',
            [
                'name' => 'floor',
                'label' => __('Floor'),
            ]
        );

        $fieldset->addField('city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'required' => true
            ]
        );

        $fieldset->addField('province_code',
            'text',
            [
                'name' => 'province_code',
                'label' => __('State\Province Code'),
            ]
        );

        $fieldset->addField('urbanization',
            'text',
            [
                'name' => 'urbanization',
                'label' => __('Urbanization'),
            ]
        );

        $fieldset->addField('postal_code',
            'text',
            [
                'name' => 'postal_code',
                'label' => __('Postal Code'),
                'required' => true
            ]
        );

        $fieldset->addField('country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'values' => $this->_countries->toOptionArray(),
                'required' => true
            ]
        );

        $fieldset->addField('residential',
            'select',
            [
                'name' => 'residential',
                'label' => __('Residential Indicator'),
                'values' => [0 => __('Non-residental (Commercial) address'), 1 => __('Residential address')],
            ]
        );

        $fieldset->addField('pickup_point',
            'text',
            [
                'name' => 'pickup_point',
                'label' => __('Pickup Point'),
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
