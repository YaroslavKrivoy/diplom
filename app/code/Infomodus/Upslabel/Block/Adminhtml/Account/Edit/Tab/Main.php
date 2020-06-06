<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Account\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Main extends Generic implements TabInterface
{
    private $_countries;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countries,
        array $data = []
    ) {
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
        return __('Account Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Account Information');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_account');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Account Information')]);
        if ($model->getId()) {
            $fieldset->addField('account_id', 'hidden', ['name' => 'account_id']);
        }
        $fieldset->addField('companyname',
            'text',
            [
                'name' => 'companyname',
                'label' => __('Company Name'),
                'required' => true
            ]);

        $fieldset->addField('attentionname',
            'text',
            [
                'name' => 'attentionname',
                'label' => __('Attention Name'),
                'required' => true
            ]);

        $fieldset->addField('address1',
            'text',
            [
                'name' => 'address1',
                'label' => __('Address 1'),
                'required' => true
            ]);

        $fieldset->addField('address2',
            'text',
            [
                'name' => 'address2',
                'label' => __('Address 2')
            ]);

        $fieldset->addField('address3',
            'text',
            [
                'name' => 'address3',
                'label' => __('Address 3')
            ]);

        $fieldset->addField('country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country/Territory'),
                'title' => __('Country/Territory'),
                'values' => $this->_countries->toOptionArray(),
                'required' => true
            ]);

        $fieldset->addField('postalcode',
            'text',
            [
                'name' => 'postalcode',
                'label' => __('Postal Code'),
                'required' => true
            ]);

        $fieldset->addField('city',
            'text',
            [
                'name' => 'city',
                'label' => __('City or Town'),
                'required' => true
            ]);

        $fieldset->addField('province',
            'text',
            [
                'name' => 'province',
                'label' => __('State/Province/County'),
                'required' => true
            ]);

        $fieldset->addField('telephone',
            'text',
            [
                'name' => 'telephone',
                'label' => __('Telephone'),
                'required' => true
            ]);

        $fieldset->addField('fax',
            'text',
            [
                'name' => 'fax',
                'label' => __('Fax')
            ]);

        $fieldset->addField('accountnumber',
            'text',
            [
                'name' => 'accountnumber',
                'label' => __('UPS Acct #'),
                'required' => true
            ]);
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
