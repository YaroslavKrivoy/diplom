<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Items\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Package extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Package Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Package Information');
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
        $form->setHtmlIdPrefix('item_');
        if(!is_array($model['handy']->defPackageParams) || count($model['handy']->defPackageParams) == 0){
            $model['handy']->defPackageParams = [[]];
        }

        foreach ($model['handy']->defPackageParams as $key => $package) {
            $fieldset = $form->addFieldset('package_fieldset_' . $key . '_', ['legend' => __('Package') . ' '. ($key + 1)]);
            $fieldset->addField(
                'packagingtypecode_' . $key . '_',
                'select',
                ['name' => 'package[packagingtypecode][]',
                    'label' => __('Packaging Type Code'),
                    'title' => __('Packaging Type Code'),
                    'required' => true,
                    'options' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Upspackagecode')->getPackagingtypecode(),
                    'value' => isset($package['packagingtypecode'])?$package['packagingtypecode']:null,
                ]
            );
            $fieldset->addField(
                'packagingdescription_' . $key . '_',
                'text',
                ['name' => 'package[packagingdescription][]',
                    'label' => __('Packaging Description'),
                    'title' => __('Packaging Description'),
                    'required' => true,
                    'value' => isset($package['packagingdescription'])?$package['packagingdescription']:null,
                ]
            );
            $fieldset->addField(
                'packagingreferencenumbercode_' . $key . '_',
                'text',
                ['name' => 'package[packagingreferencenumbercode][]',
                    'label' => __('Packaging Reference Number Code'),
                    'title' => __('Packaging Reference Number Code'),
                    'required' => false,
                    'value' => isset($package['packagingreferencenumbercode'])?$package['packagingreferencenumbercode']:null,
                ]
            );
            $fieldset->addField(
                'packagingreferencenumbervalue_' . $key . '_',
                'text',
                ['name' => 'package[packagingreferencenumbervalue][]',
                    'label' => __('Packaging Reference Number Value'),
                    'title' => __('Packaging Reference Number Value'),
                    'required' => false,
                    'value' => isset($package['packagingreferencenumbervalue'])?$package['packagingreferencenumbervalue']:null,
                ]
            );
            $fieldset->addField(
                'packagingreferencenumbercode2_' . $key . '_',
                'text',
                ['name' => 'package[packagingreferencenumbercode2][]',
                    'label' => __('Packaging Reference Number Code 2'),
                    'title' => __('Packaging Reference Number Code 2'),
                    'required' => false,
                    'value' => isset($package['packagingreferencenumbercode2'])?$package['packagingreferencenumbercode2']:null,
                ]
            );
            $fieldset->addField(
                'packagingreferencenumbervalue2_' . $key . '_',
                'text',
                ['name' => 'package[packagingreferencenumbervalue2][]',
                    'label' => __('Packaging Reference Number Value 2'),
                    'title' => __('Packaging Reference Number Value 2'),
                    'required' => false,
                    'value' => isset($package['packagingreferencenumbervalue2'])?$package['packagingreferencenumbervalue2']:null,
                ]
            );
            $fieldset->addField(
                'weight_' . $key . '_',
                'text',
                ['name' => 'package[weight][]',
                    'label' => __('Weight'),
                    'title' => __('Weight'),
                    'required' => true,
                    'value' => isset($package['weight'])?$package['weight']:null,
                ]
            );
            $fieldset->addField(
                'packweight_' . $key . '_',
                'text',
                ['name' => 'package[packweight][]',
                    'label' => __('Pack weight'),
                    'title' => __('Pack weight'),
                    'required' => false,
                    'value' => isset($package['packweight'])?$package['packweight']:null,
                ]
            );
            $fieldset->addField(
                'additionalhandling_' . $key . '_',
                'select',
                ['name' => 'package[additionalhandling][]',
                    'label' => __('Additional Handling'),
                    'title' => __('Additional Handling'),
                    'required' => false,
                    'options' => [__('No'), __('Yes')],
                    'value' => isset($package['additionalhandling'])?$package['additionalhandling']:null,
                ]
            );
            $fieldset->addField(
                'default_box_' . $key . '_',
                'select',
                ['name' => 'package[box][]',
                    'label' => __('Box'),
                    'title' => __('Box'),
                    'required' => false,
                    'values' => $model['handy']->objectManager->get('Infomodus\Upslabel\Model\Config\Boxes')->toOptionArray(),
                    'value' => isset($package['box']) ? $package['box'] : null,
                    'class' => 'box-selected'
                ]
            );
            $fieldset->addField(
                'length_' . $key . '_',
                'text',
                ['name' => 'package[length][]',
                    'label' => __('Length'),
                    'title' => __('Length'),
                    'required' => false,
                    'value' => isset($package['length'])?$package['length']:null,
                    'class' => 'box-length'
                ]
            );
            $fieldset->addField(
                'width_' . $key . '_',
                'text',
                ['name' => 'package[width][]',
                    'label' => __('Width'),
                    'title' => __('Width'),
                    'required' => false,
                    'value' => isset($package['width'])?$package['width']:null,
                    'class' => 'box-width'
                ]
            );
            $fieldset->addField(
                'height_' . $key . '_',
                'text',
                ['name' => 'package[height][]',
                    'label' => __('Height'),
                    'title' => __('Height'),
                    'required' => false,
                    'value' => isset($package['height'])?$package['height']:null,
                    'class' => 'box-height'
                ]
            );
            if ($model['handy']->type != 'refund') {
                $fieldset->addField(
                    'cod_' . $key . '_',
                    'select',
                    ['name' => 'package[cod][]',
                        'label' => __('COD'),
                        'title' => __('COD'),
                        'required' => false,
                        'options' => [__('No'), __('Yes')],
                        'value' => isset($package['cod'])?$package['cod']:null,
                    ]
                );
                $fieldset->addField(
                    'codfundscode_' . $key . '_',
                    'select',
                    ['name' => 'package[codfundscode][]',
                        'label' => __('COD Funds code'),
                        'title' => __('COD Funds code'),
                        'required' => false,
                        'options' => [0 => __('check, cashiers check or money order - no cash allowed'), 8 => __('cashiers check or money order - no cash allowed')],
                        'value' => isset($package['codfundscode'])?$package['codfundscode']:null,
                    ]
                );
            }
            $fieldset->addField(
                'codmonetaryvalue_' . $key . '_',
                'text',
                ['name' => 'package[codmonetaryvalue][]',
                    'label' => __('Monetary value'),
                    'title' => __('Monetary value'),
                    'required' => false,
                    'value' => isset($package['codmonetaryvalue'])?round($package['codmonetaryvalue'], 2):null,
                ]
            );
            $fieldset->addField(
                'insuredmonetaryvalue_' . $key . '_',
                'text',
                ['name' => 'package[insuredmonetaryvalue][]',
                    'label' => __('Insured Monetary value'),
                    'title' => __('Insured Monetary value'),
                    'required' => false,
                    'value' => isset($package['insuredmonetaryvalue'])?round($package['insuredmonetaryvalue'], 2):null,
                ]
            );
        }
        /*$form->setValues($model->getData());*/
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
