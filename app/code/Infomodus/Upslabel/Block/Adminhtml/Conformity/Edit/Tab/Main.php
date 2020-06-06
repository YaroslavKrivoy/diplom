<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Conformity\Edit\Tab;

use Infomodus\Upslabel\Model\Config\Pickup\Stores;
use Infomodus\Upslabel\Model\Config\ShippingMethods;
use Infomodus\Upslabel\Model\Config\Upsmethod;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Main extends Generic implements TabInterface
{
    private $_countries;
    public $storeId;
    private $shippingMethods;
    private $upsmethod;
    private $stores;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countries,
        ShippingMethods $shippingMethods,
        Upsmethod $upsmethod,
        Stores $stores,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->_countries = $countries;
        $this->shippingMethods = $shippingMethods;
        $this->upsmethod = $upsmethod;
        $this->stores = $stores;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Compliance Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Compliance Information');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_conformity');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Compliance Information')]);
        if ($model->getId()) {
            $fieldset->addField('conformity_id', 'hidden', ['name' => 'conformity_id']);
        }

        $this->storeId = $this->getRequest()->getParam('store', 1);

        $fieldset->addField('method_id', 'select', [
            'name' => 'method_id',
            'label' => __('Shipping Method in Checkout'),
            'title' => __('Shipping Method in Checkout'),
            'required' => true,
            'values' => $this->shippingMethods->toOptionArray(),
        ]);

        $fieldset->addField('upsmethod_id', 'select', [
            'name' => 'upsmethod_id',
            'label' => __('UPS Shipping Service for labels'),
            'title' => __('UPS Shipping Service for labels'),
            'required' => true,
            'values' => $this->upsmethod->toOptionArray(),
        ]);

        $fieldset->addField('country_ids', 'multiselect', [
            'name' => 'country_ids',
            'label' => __('Allowed Countries'),
            'title' => __('Allowed Countries'),
            'required' => true,
            'values' => $this->_countries->toOptionArray(),
        ]);

        $fieldset->addField('store_id', 'select', [
            'name' => 'store_id',
            'label' => __('Apply to Store'),
            'value' => $this->storeId,
            'values' => $this->stores->toOptionArray(),
            /*'disabled' => true,*/
        ]);

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
