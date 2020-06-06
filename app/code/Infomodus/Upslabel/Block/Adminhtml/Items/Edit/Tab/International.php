<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class International extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('International Paperless Invoice');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('International Paperless Invoice');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_items');
        $confParams = $model['handy']->defConfParams;
        $address = $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Defaultaddress')->getAddressesById($confParams['shipfrom_no']);
        if(!empty($address) && ($confParams['shiptocountrycode'] == $address->getCountry()
            || $model['handy']->type == 'refund')
        ){
            return true;
        }
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
        $fieldset = $form->addFieldset('international_fieldset', ['legend' => __('Configuration')]);
        $address = $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Defaultaddress')->getAddressesById($confParams['shipfrom_no']);
        if (!empty($address) && ($confParams['shiptocountrycode'] == $address->getCountry()
            || $model['handy']->type == 'refund')
        ) {
            $confParams['international_invoice'] = 0;
        }
        $depenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $fieldset->addField(
            'international_invoice',
            'select',
            ['name' => 'international_invoice',
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'required' => true,
                'options' => [__('No'), __('Yes')],
                'value' => $confParams['international_invoice']
            ]
        );
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_invoice",
            'international_invoice'
        );

        $fieldset->addField(
            'international_comments',
            'textarea',
            ['name' => 'international_comments',
                'label' => __('Comments'),
                'title' => __('Comments'),
                'required' => false,
                'value' => $confParams['international_comments']
            ]
        );
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_comments",
            'international_comments'
        )->addFieldDependence(
            'international_comments',
            'international_invoice',
            '1'
        );

        $fieldset->addField(
            'international_invoicenumber',
            'text',
            ['name' => 'international_invoicenumber',
                'label' => __('Invoice number'),
                'title' => __('Invoice number'),
                'required' => false,
                'value' => $confParams['international_invoicenumber']
            ]
        );
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_invoicenumber",
            'international_invoicenumber'
        )->addFieldDependence(
            'international_invoicenumber',
            'international_invoice',
            '1'
        );

        $fieldset->addField(
            'international_invoicedate',
            'date',
            ['name' => 'international_invoicedate',
                'label' => __('Invoice date'),
                'title' => __('Invoice date'),
                'required' => true,
                'date_format' => 'Y-MM-dd',
                'value' => $confParams['international_invoicedate'],
            ]
        );
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_invoicedate",
            'international_invoicedate'
        )->addFieldDependence(
            'international_invoicedate',
            'international_invoice',
            '1'
        );

        $fieldset->addField(
            'international_reasonforexport',
            'select',
            ['name' => 'international_reasonforexport',
                'label' => __('Reason for export'),
                'title' => __('Reason for export'),
                'required' => true,
                'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\ReasonForExport')->toOptionArray(),
                'value' => $confParams['international_reasonforexport'],
            ]
        );
        /*$fieldset->addField(
            'international_sold_to',
            'select',
            ['name' => 'international_sold_to',
                'label' => __('Sold to'),
                'title' => __('Sold to'),
                'required' => true,
                'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\SoldTo')->toOptionArray(),
                'value' => $confParams['international_sold_to']
            ]
        );*/
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_reasonforexport",
            'international_reasonforexport'
        )->addFieldDependence(
            'international_reasonforexport',
            'international_invoice',
            '1'
        );

        $fieldset->addField(
            'international_purchaseordernumber',
            'text',
            ['name' => 'international_purchaseordernumber',
                'label' => __('Purchase order number'),
                'title' => __('Purchase order number'),
                'required' => false,
                'value' => $confParams['international_purchaseordernumber']
            ]
        );
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_purchaseordernumber",
            'international_purchaseordernumber'
        )->addFieldDependence(
            'international_purchaseordernumber',
            'international_invoice',
            '1'
        );

        $fieldset->addField(
            'international_termsofshipment',
            'select',
            ['name' => 'international_termsofshipment',
                'label' => __('Terms of shipment'),
                'title' => __('Terms of shipment'),
                'required' => false,
                'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\TermsOfShipment')->toOptionArray(),
                'value' => $confParams['international_termsofshipment']
            ]
        );
        $depenceBlock->addFieldMap(
            "{$htmlIdPrefix}international_termsofshipment",
            'international_termsofshipment'
        )->addFieldDependence(
            'international_termsofshipment',
            'international_invoice',
            '1'
        );

        $fieldset->addField(
            'declaration_statement',
            'text',
            ['name' => 'declaration_statement',
                'label' => __('Declaration Statement'),
                'title' => __('Declaration Statement'),
                'required' => false,
                'value' => $confParams['declaration_statement']
            ]
        );

        if (isset($confParams['international_products']) && count($confParams['international_products']) > 0) {

            foreach ($confParams['international_products'] as $key => $product) {
                $fieldsetProducts = $form->addFieldset('international_products_fieldset' . $key, ['legend' => __('Products') . (" " . ($key + 1))]);
                $fieldsetProducts->addField(
                    'international_products-enabled' . $key,
                    'select',
                    ['name' => 'international_products[' . $key . '][enabled]',
                        'label' => __('Enabled'),
                        'title' => __('Enabled'),
                        'required' => true,
                        'options' => [__('No'), __('Yes')],
                        'value' => $product['enabled']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-description' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][description]',
                        'label' => __('Description'),
                        'title' => __('Description'),
                        'required' => false,
                        'value' => $product['description']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-country_code' . $key,
                    'select',
                    ['name' => 'international_products[' . $key . '][country_code]',
                        'label' => __('Origin Country Code'),
                        'title' => __('Origin Country Code'),
                        'required' => false,
                        'values' => $model['handy']->objectManager->get('Magento\Directory\Model\Config\Source\Country')->toOptionArray(),
                        'value' => $product['country_code']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-qty' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][qty]',
                        'label' => __('Quantity'),
                        'title' => __('Quantity'),
                        'required' => false,
                        'value' => $product['qty']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-amount' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][amount]',
                        'label' => __('Amount'),
                        'title' => __('Amount'),
                        'required' => false,
                        'value' => round($product['amount'], 2)
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-unit_of_measurement' . $key,
                    'select',
                    ['name' => 'international_products[' . $key . '][unit_of_measurement]',
                        'label' => __('Unit of measurement'),
                        'title' => __('Unit of measurement'),
                        'required' => false,
                        'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\InternationalUnitofmeasurement')->toOptionArray(),
                        'value' => $product['unit_of_measurement']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-unit_of_measurement_desc' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][unit_of_measurement_desc]',
                        'label' => __('Unit of measurement description'),
                        'title' => __('Unit of measurement description'),
                        'required' => false,
                        'value' => $product['unit_of_measurement_desc']
                    ]
                );
                $depenceBlock->addFieldMap(
                    "{$htmlIdPrefix}international_products-unit_of_measurement" . $key,
                    'international_products-unit_of_measurement' . $key

                )->addFieldMap(
                    "{$htmlIdPrefix}international_products-unit_of_measurement_desc" . $key,
                    'international_products-unit_of_measurement_desc' . $key
                )->addFieldDependence(
                    'international_products-unit_of_measurement_desc' . $key,
                    'international_products-unit_of_measurement' . $key,
                    'OTH'
                );
                $fieldsetProducts->addField(
                    'international_products-commoditycode' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][commoditycode]',
                        'label' => __('Commodity code'),
                        'title' => __('Commodity code'),
                        'required' => false,
                        'value' => $product['commoditycode']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-partnumber' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][partnumber]',
                        'label' => __('Part number'),
                        'title' => __('Part number'),
                        'required' => false,
                        'value' => $product['partnumber']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-scheduleB_number' . $key,
                    'text',
                    ['name' => 'international_products[' . $key . '][scheduleB_number]',
                        'label' => __('ScheduleB number'),
                        'title' => __('ScheduleB number'),
                        'required' => false,
                        'value' => $product['scheduleB_number']
                    ]
                );
                $fieldsetProducts->addField(
                    'international_products-scheduleB_unit' . $key,
                    'select',
                    ['name' => 'international_products[' . $key . '][scheduleB_unit]',
                        'label' => __('ScheduleB Unit Of Measurement'),
                        'title' => __('ScheduleB Unit Of Measurement'),
                        'required' => false,
                        'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\SchedulebUnitofmeasurement')->toOptionArray(),
                        'value' => $product['scheduleB_unit']
                    ]
                );
            }
        }
        $this->setChild(
            'form_after',
            $depenceBlock
        );
        /*$form->setValues($model->getData());*/
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
