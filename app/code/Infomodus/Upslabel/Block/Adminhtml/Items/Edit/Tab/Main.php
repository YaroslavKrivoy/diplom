<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Main extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Main options');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Main options');
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
        $fieldset = $form->addFieldset('configuration_fieldset', ['legend' => __('Configuration options')]);
        $fieldset->addField(
            'upsaccount',
            'select',
            ['name' => 'upsaccount',
                'label' => __('Who pay for Shipment?'),
                'title' => __('Who pay for Shipment?'),
                'required' => true,
                'options' => $model['handy']->upsAccounts,
                'value' => $confParams['upsaccount']
            ]
        );
        $fieldset->addField(
            'serviceCode',
            'select',
            ['name' => 'serviceCode',
                'label' => __('UPS shipping method'),
                'title' => __('UPS shipping method'),
                'required' => true,
                'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Upsmethod')->getUpsMethods($model['handy']->storeId),
                'value' => $confParams['serviceCode']
            ]
        );
        $fieldset->addField(
            'shipper_no',
            'select',
            ['name' => 'shipper_no',
                'label' => __('Shipper address'),
                'title' => __('Shipper address'),
                'required' => true,
                'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Defaultaddress')->getAddresses(),
                'value' => $confParams['shipper_no']
            ]
        );
        $addressName = ($model['handy']->type == 'shipment'?'Ship from address':'Ship to address');
        $fieldset->addField(
            'shipfrom_no',
            'select',
            ['name' => 'shipfrom_no',
                'label' => __($addressName),
                'title' => __($addressName),
                'required' => true,
                'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Defaultaddress')->getAddresses(),
                'value' => $confParams['shipfrom_no']
            ]
        );
        $fieldset->addField(
            'testing',
            'select',
            ['name' => 'testing',
                'label' => __('Test mode'),
                'title' => __('Test mode'),
                'required' => true,
                'options' => [__('No'), __('Yes')],
                'value' => $confParams['testing']
            ]
        );
        if ($model['handy']->type == 'shipment' || $model['handy']->type == 'invert') {
            $fieldset->addField(
                'addtrack',
                'select',
                ['name' => 'addtrack',
                    'label' => __('Add tracking number automatically ?'),
                    'title' => __('Add tracking number automatically ?'),
                    'required' => false,
                    'options' => [__('No'), __('Yes')],
                    'value' => $confParams['addtrack']
                ]
            );
        }
        $fieldset->addField(
            'shipmentdescription',
            'textarea',
            ['name' => 'shipmentdescription',
                'label' => __('Shipment Description'),
                'title' => __('Shipment Description'),
                'required' => false,
                'value' => $confParams['shipmentdescription']
            ]
        );
        $fieldset->addField(
            'currencycode',
            'select',
            ['name' => 'currencycode',
                'label' => __('Currency code'),
                'title' => __('Currency code'),
                'required' => false,
                'values' => $model['handy']->objectManager->get('Magento\Config\Model\Config\Source\Locale\Currency\All')->toOptionArray(),
                'value' => $confParams['currencycode']
            ]
        );
        if ($model['handy']->type != 'refund') {
            $fieldset->addField(
                'shipmentcharge',
                'select',
                ['name' => 'shipmentcharge',
                    'label' => __('Duty and Tax charges'),
                    'title' => __('Duty and Tax charges'),
                    'required' => false,
                    'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\DutyAndTaxInternational')->toOptionArray(),
                    'value' => $confParams['shipmentcharge']
                ]
            );
            $fieldset->addField(
                'cod',
                'select',
                ['name' => 'cod',
                    'label' => __('COD'),
                    'title' => __('COD'),
                    'required' => false,
                    'options' => [__('No'), __('Yes')],
                    'value' => $confParams['cod']
                ]
            );
            $fieldset->addField(
                'codfundscode',
                'select',
                ['name' => 'codfundscode',
                    'label' => __('COD Funds code'),
                    'title' => __('COD Funds code'),
                    'required' => false,
                    'options' => [1 => __('cash'), 9 => __('check, cashiers check or money order - no cash allowed')],
                    'value' => $confParams['codfundscode']
                ]
            );
        }
        $fieldset->addField(
            'codmonetaryvalue',
            'text',
            ['name' => 'codmonetaryvalue',
                'label' => __('Monetary value'),
                'title' => __('Monetary value'),
                'required' => false,
                'value' => round($confParams['codmonetaryvalue'], 2)
            ]
        );
        $fieldset->addField(
            'invoicelinetotalyesno',
            'select',
            ['name' => 'invoicelinetotalyesno',
                'label' => __('Send InvoiceLineTotal'),
                'title' => __('Send InvoiceLineTotal'),
                'required' => false,
                'options' => [__('No'), __('Yes')],
                'value' => $confParams['invoicelinetotalyesno'],
                'after_element_html' => __('(for shipments from US to Canada and PR only)')
            ]
        );
        $fieldset->addField(
            'invoicelinetotal',
            'text',
            ['name' => 'invoicelinetotal',
                'label' => __('Invoice Line Total Monetary value'),
                'title' => __('Invoice Line Total Monetary value'),
                'required' => false,
                'value' => round($confParams['invoicelinetotal'], 2)
            ]
        );
        $fieldset->addField(
            'carbon_neutral',
            'select',
            ['name' => 'carbon_neutral',
                'label' => __('Carbon neutral'),
                'title' => __('Carbon neutral'),
                'required' => false,
                'options' => [__('No'), __('Yes')],
                'value' => $confParams['carbon_neutral']
            ]
        );
        if ($model['handy']->type == 'shipment'/* && $model['handy']->shipment_id !== null*/) {
            $fieldset->addField(
                'default_return',
                'select',
                ['name' => 'default_return',
                    'label' => __('Create return label now'),
                    'title' => __('Create return label now'),
                    'required' => false,
                    'options' => [__('No'), __('Yes')],
                    'value' => $confParams['default_return']
                ]
            );
            $fieldset->addField(
                'default_return_servicecode',
                'select',
                ['name' => 'default_return_servicecode',
                    'label' => __('UPS shipping method for return label'),
                    'title' => __('UPS shipping method for return label'),
                    'required' => false,
                    'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Upsmethod')->getUpsMethods($model['handy']->storeId),
                    'value' => $confParams['default_return_servicecode']
                ],
                'default_return'
            );
            $this->setChild(
                'form_after',
                $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Widget\Form\Element\Dependence'
                )->addFieldMap(
                    "{$htmlIdPrefix}default_return",
                    'default_return'
                )->addFieldMap(
                    "{$htmlIdPrefix}default_return_servicecode",
                    'default_return_servicecode'
                )->addFieldDependence(
                    'default_return_servicecode',
                    'default_return',
                    '1'
                )
            );
        }
        $fieldset->addField(
            'qvn',
            'select',
            ['name' => 'qvn',
                'label' => __('Quantum View Notification Enable'),
                'title' => __('Quantum View Notification Enable'),
                'required' => false,
                'options' => [__('No'), __('Yes')],
                'value' => $confParams['qvn']
            ],
            'to'
        );
        $fieldset->addField(
            'qvn_code',
            'multiselect',
            ['name' => 'qvn_code',
                'label' => __('Notification Code'),
                'title' => __('Notification Code'),
                'required' => false,
                'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Notificationcode')->toOptionArray(),
                'value' => $confParams['qvn_code']
            ],
            'qvn'
        );
        $fieldset->addField(
            'qvn_email_shipper',
            'text',
            ['name' => 'qvn_email_shipper',
                'label' => __('Shipper Email'),
                'title' => __('Shipper Email'),
                'required' => false,
                'value' => $confParams['qvn_email_shipper']
            ]
        );
        $fieldset->addField(
            'qvn_lang',
            'select',
            ['name' => 'qvn_lang',
                'label' => __('Notification Lang'),
                'title' => __('Notification Lang'),
                'required' => false,
                'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\NotificationLang')->toOptionArray(),
                'value' => $confParams['qvn_lang']
            ],
            'qvn_email_shipper'
        );
        $fieldset->addField(
            'adult',
            'select',
            ['name' => 'adult',
                'label' => __('Delivery Confirmation'),
                'title' => __('Delivery Confirmation'),
                'required' => false,
                'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\DeliveryConfirmation')->getDeliveryConfirmation(),
                'value' => $confParams['adult']
            ]
        );
        $fieldset->addField(
            'saturday_delivery',
            'select',
            ['name' => 'saturday_delivery',
                'label' => __('Saturday Delivery'),
                'title' => __('Saturday Delivery'),
                'required' => false,
                'options' => [__('No'), __('Yes')],
                'value' => $confParams['saturday_delivery']
            ]
        );
        $fieldset->addField(
            'movement_reference_number_enabled',
            'select',
            ['name' => 'movement_reference_number_enabled',
                'label' => __('Movement Enabled'),
                'title' => __('Movement Enabled'),
                'required' => false,
                'options' => [__('No'), __('Yes')],
                'value' => (isset($confParams['movement_reference_number_enabled']) && isset($confParams['movement_reference_number']) && $confParams['movement_reference_number'] != '' && $confParams['movement_reference_number_enabled'] > 0)?1:0
            ]
        );
        $fieldset->addField(
            'movement_reference_number',
            'text',
            ['name' => 'movement_reference_number',
                'label' => __('Movement Reference Number'),
                'title' => __('Movement Reference Number'),
                'required' => false,
                'value' => $confParams['movement_reference_number']
            ]
        );


        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "{$htmlIdPrefix}qvn",
                'qvn'
            )->addFieldMap(
                "{$htmlIdPrefix}qvn_code",
                'qvn_code'
            )->addFieldMap(
                "{$htmlIdPrefix}qvn_lang",
                'qvn_lang'
            )->addFieldDependence(
                'qvn_code',
                'qvn',
                '1'
            )->addFieldDependence(
                'qvn_lang',
                'qvn',
                '1'
            )
        );
        /*$form->setValues($model->getData());*/
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
