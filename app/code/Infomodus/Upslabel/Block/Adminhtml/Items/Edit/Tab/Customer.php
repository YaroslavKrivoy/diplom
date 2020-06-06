<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Customer extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Customer options');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Customer options');
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
        $fieldset = $form->addFieldset('customer_fieldset', ['legend' => __('Customer options')]);
        $fieldset->addField(
            'residentialaddress',
            'select',
            ['name' => 'residentialaddress',
                'label' => __('Residential address'),
                'title' => __('Residential address'),
                'required' => true,
                'options' => [0 => __('Auto'), 2 => __('No'), 1 => __('Yes')],
                'value' => $confParams['residentialaddress']
            ]
        );
        $fieldset->addField(
            'shiptocompanyname',
            'text',
            ['name' => 'shiptocompanyname',
                'label' => __('Company name'),
                'title' => __('Company name'),
                'required' => true,
                'value' => $confParams['shiptocompanyname']
            ]
        );
        $fieldset->addField(
            'shiptoattentionname',
            'text',
            ['name' => 'shiptoattentionname',
                'label' => __('Attention name'),
                'title' => __('Attention name'),
                'required' => true,
                'value' => $confParams['shiptoattentionname']
            ]
        );
        $fieldset->addField(
            'shiptophonenumber',
            'text',
            ['name' => 'shiptophonenumber',
                'label' => __('Phone number'),
                'title' => __('Phone number'),
                'required' => true,
                'value' => $confParams['shiptophonenumber']
            ]
        );
        $fieldset->addField(
            'shiptoaddressline1',
            'text',
            ['name' => 'shiptoaddressline1',
                'label' => __('Address line'),
                'title' => __('Address line'),
                'required' => true,
                'value' => $confParams['shiptoaddressline1']
            ]
        );
        $fieldset->addField(
            'shiptoaddressline2',
            'text',
            ['name' => 'shiptoaddressline2',
                'label' => __('Address line 2'),
                'title' => __('Address line 2'),
                'required' => false,
                'value' => $confParams['shiptoaddressline2']
            ]
        );
        $fieldset->addField(
            'shiptocity',
            'text',
            ['name' => 'shiptocity',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
                'value' => $confParams['shiptocity']
            ]
        );
        $fieldset->addField(
            'shiptostateprovincecode',
            'text',
            ['name' => 'shiptostateprovincecode',
                'label' => __('State (province)'),
                'title' => __('State (province)'),
                'required' => false,
                'value' => $confParams['shiptostateprovincecode']
            ]
        );
        $fieldset->addField(
            'shiptopostalcode',
            'text',
            ['name' => 'shiptopostalcode',
                'label' => __('Postal code'),
                'title' => __('Postal code'),
                'required' => true,
                'value' => $confParams['shiptopostalcode']
            ]
        );
        $fieldset->addField(
            'shiptocountrycode',
            'select',
            ['name' => 'shiptocountrycode',
                'label' => __('Country code'),
                'title' => __('Country code'),
                'required' => true,
                'values' => $model['handy']->objectManager->get('Magento\Directory\Model\Config\Source\Country')->toOptionArray(),
                'value' => $confParams['shiptocountrycode']
            ]
        );
        $fieldset->addField(
            'qvn_email_shipto',
            'text',
            ['name' => 'qvn_email_shipto',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'value' => $confParams['qvn_email_shipto']
            ]
        );
        if (isset($confParams['accesspoint'])) {
            $fieldset->addField(
                'accesspoint',
                'hidden',
                ['name' => 'accesspoint',
                    'value' => $confParams['accesspoint']
                ]
            );
            if (isset($confParams['accesspoint_type'])) {
                $fieldset->addField(
                    'accesspoint_type',
                    'hidden',
                    ['name' => 'accesspoint_type',
                        'value' => $confParams['accesspoint_type']
                    ]
                );
            }
            if (isset($confParams['accesspoint_name'])) {
                $fieldset->addField(
                    'accesspoint_name',
                    'hidden',
                    ['name' => 'accesspoint_name',
                        'value' => $confParams['accesspoint_name']
                    ]
                );
            }
            if (isset($confParams['accesspoint_atname'])) {
                $fieldset->addField(
                    'accesspoint_atname',
                    'hidden',
                    ['name' => 'accesspoint_atname',
                        'value' => $confParams['accesspoint_atname']
                    ]
                );
            }
            if (isset($confParams['accesspoint_appuid'])) {
                $fieldset->addField(
                    'accesspoint_appuid',
                    'hidden',
                    ['name' => 'accesspoint_appuid',
                        'value' => $confParams['accesspoint_appuid']
                    ]
                );
            }
            if (isset($confParams['accesspoint_street'])) {
                $fieldset->addField(
                    'accesspoint_street',
                    'hidden',
                    ['name' => 'accesspoint_street',
                        'value' => $confParams['accesspoint_street']
                    ]
                );
            }
            if (isset($confParams['accesspoint_street1'])) {
                $fieldset->addField(
                    'accesspoint_street1',
                    'hidden',
                    ['name' => 'accesspoint_street1',
                        'value' => $confParams['accesspoint_street1']
                    ]
                );
            }
            if (isset($confParams['accesspoint_street2'])) {
                $fieldset->addField(
                    'accesspoint_street2',
                    'hidden',
                    ['name' => 'accesspoint_street2',
                        'value' => $confParams['accesspoint_street2']
                    ]
                );
            }
            if (isset($confParams['accesspoint_city'])) {
                $fieldset->addField(
                    'accesspoint_city',
                    'hidden',
                    ['name' => 'accesspoint_city',
                        'value' => $confParams['accesspoint_city']
                    ]
                );
            }
            if (isset($confParams['accesspoint_provincecode'])) {
                $fieldset->addField(
                    'accesspoint_provincecode',
                    'hidden',
                    ['name' => 'accesspoint_provincecode',
                        'value' => $confParams['accesspoint_provincecode']
                    ]
                );
            }
            if (isset($confParams['accesspoint_postal'])) {
                $fieldset->addField(
                    'accesspoint_postal',
                    'hidden',
                    ['name' => 'accesspoint_postal',
                        'value' => $confParams['accesspoint_postal']
                    ]
                );
            }
            if (isset($confParams['accesspoint_country'])) {
                $fieldset->addField(
                    'accesspoint_country',
                    'hidden',
                    ['name' => 'accesspoint_country',
                        'value' => $confParams['accesspoint_country']
                    ]
                );
            }
        }

        /*$form->setValues($model->getData());*/
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
