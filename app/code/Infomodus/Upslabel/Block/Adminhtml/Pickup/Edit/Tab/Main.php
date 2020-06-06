<?php
/**
 * Copyright © 2015 Infomodus. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Infomodus\Upslabel\Block\Adminhtml\Pickup\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;


class Main extends Generic implements TabInterface
{
    private $_countries;
    private $_config;
    private $storeId = null;
    private $stores;
    private $yesNo;
    private $year;
    private $residential;
    private $defaultAddress;
    private $paymentmethod;
    private $overweightindicator;
    private $weight;
    private $containercode;
    private $servicecode;
    private $alternateindicator;
    private $pickupDay;
    private $pickupMonth;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countries,
        \Infomodus\Upslabel\Helper\Config $config,
        \Infomodus\Upslabel\Model\Config\Pickup\Stores $stores,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Infomodus\Upslabel\Model\Config\Pickup\Year $year,
        \Infomodus\Upslabel\Model\Config\Pickup\Residential $residential,
        \Infomodus\Upslabel\Model\Config\Defaultaddress $defaultAddress,
        \Infomodus\Upslabel\Model\Config\Pickup\Paymentmethod $paymentmethod,
        \Infomodus\Upslabel\Model\Config\Pickup\Overweightindicator $overweightindicator,
        \Infomodus\Upslabel\Model\Config\Weight $weight,
        \Infomodus\Upslabel\Model\Config\Pickup\Containercode $containercode,
        \Infomodus\Upslabel\Model\Config\Pickup\Servicecode $servicecode,
        \Infomodus\Upslabel\Model\Config\Pickup\Alternateindicator $alternateindicator,
        \Infomodus\Upslabel\Model\Config\Pickup\Day $pickupDay,
        \Infomodus\Upslabel\Model\Config\Pickup\Month $pickupMonth,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->_countries = $countries;
        $this->_config = $config;
        $this->stores = $stores;
        $this->yesNo = $yesNo;
        $this->year = $year;
        $this->residential = $residential;
        $this->defaultAddress = $defaultAddress;
        $this->paymentmethod = $paymentmethod;
        $this->overweightindicator = $overweightindicator;
        $this->weight = $weight;
        $this->containercode = $containercode;
        $this->servicecode = $servicecode;
        $this->alternateindicator = $alternateindicator;
        $this->pickupDay = $pickupDay;
        $this->pickupMonth = $pickupMonth;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Pickup Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Pickup Information');
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
        $model = $this->_coreRegistry->registry('current_infomodus_upslabel_pickup');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Pickup Information')]);
        if ($model->getId()) {
            $fieldset->addField('pickup_id', 'hidden', ['name' => 'pickup_id']);
        }

        if ($this->getRequest()->getParam('store_id')) {
            $this->storeId = (int)$this->getRequest()->getParam('store_id');
        }
        $fieldset->addField('store',
            'select',
            [
                'name' => 'store',
                'label' => __('Store'),
                'value' => $this->storeId,
                'values' => $this->stores->toOptionArray(),
            ]);

        if ($this->getRequest()->getParam('id') > 0) {
            $fieldset->addField('price',
                'text',
                [
                    'label' => __('Grand Total Of All Charge'),
                    'disabled' => true,
                ]);
        }
        $fieldset->addField('CloseTime',
            'time',
            [
                'name' => 'CloseTime',
                'label' => __('Close Time'),
                'title' => __('Close Time'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/CloseTime', $this->storeId),
            ]);

        $fieldset->addField('ReadyTime',
            'time',
            [
                'name' => 'ReadyTime',
                'label' => __('Ready Time'),
                'title' => __('Ready Time'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/ReadyTime', $this->storeId),
            ]);

        $fieldset->addField('PickupDateYear',
            'select',
            [
                'name' => 'PickupDateYear',
                'label' => __('Pickup Date Year'),
                'title' => __('Pickup Date Year'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/PickupDateYear', $this->storeId),
                'values' => $this->year->toOptionArray(),
            ]);

        $fieldset->addField('PickupDateMonth',
            'select',
            [
                'name' => 'PickupDateMonth',
                'label' => __('Pickup Date Month'),
                'title' => __('Pickup Date Month'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/PickupDateMonth', $this->storeId),
                'values' => $this->pickupMonth->toOptionArray(),
            ]);

        $fieldset->addField('PickupDateDay',
            'select',
            [
                'name' => 'PickupDateDay',
                'label' => __('Pickup Date Day'),
                'title' => __('Pickup Date Day'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/PickupDateDay', $this->storeId),
                'values' => $this->pickupDay->toOptionArray(),
            ]);

        $fieldset->addField('AlternateAddressIndicator',
            'select',
            [
                'name' => 'AlternateAddressIndicator',
                'label' => __('Alternate Address Indicator'),
                'title' => __('Alternate Address Indicator'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/AlternateAddressIndicator', $this->storeId),
                'values' => $this->alternateindicator->toOptionArray(),
            ]);

        $fieldset->addField('ServiceCode',
            'select',
            [
                'name' => 'ServiceCode',
                'label' => __('Service Code'),
                'title' => __('Service Code'),
                'required' => true,
                'value' => $this->_config->getStoreConfig('upslabel/pickup/ServiceCode', $this->storeId),
                'values' => $this->servicecode->toOptionArray(),
            ]);

        $fieldset->addField('Quantity', 'text', [
            'name' => 'Quantity',
            'label' => __('Quantity'),
            'title' => __('Quantity'),
            'required' => true,
            'value' => $this->_config->getStoreConfig('upslabel/pickup/Quantity', $this->storeId),
        ]);

        $fieldset->addField('DestinationCountryCode', 'select', [
            'name' => 'DestinationCountryCode',
            'label' => __('Destination Country'),
            'title' => __('Destination Country'),
            'required' => true,
            'value' => $this->_config->getStoreConfig('upslabel/pickup/DestinationCountryCode', $this->storeId),
            'values' => $this->_countries->toOptionArray(),
        ]);

        $fieldset->addField('ContainerCode', 'select', [
            'name' => 'ContainerCode',
            'label' => __('Container Code'),
            'title' => __('Container Code'),
            'required' => true,
            'value' => $this->_config->getStoreConfig('upslabel/pickup/ContainerCode', $this->storeId),
            'values' => $this->containercode->toOptionArray(),
        ]);

        $fieldset->addField('Weight', 'text', [
            'name' => 'Weight',
            'label' => __('Weight'),
            'title' => __('Weight'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/Weight', $this->storeId),
        ]);

        $fieldset->addField('UnitOfMeasurement', 'select', [
            'name' => 'UnitOfMeasurement',
            'label' => __('Unit Of Measurement'),
            'title' => __('Unit Of Measurement'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/UnitOfMeasurement', $this->storeId),
            'values' => $this->weight->toOptionArray(),
        ]);

        $fieldset->addField('OverweightIndicator', 'select', [
            'name' => 'OverweightIndicator',
            'label' => __('Overweight Indicator'),
            'title' => __('Overweight Indicator'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/OverweightIndicator', $this->storeId),
            'values' => $this->overweightindicator->toOptionArray(),
        ]);

        $fieldset->addField('PaymentMethod', 'select', [
            'name' => 'PaymentMethod',
            'label' => __('Payment Method'),
            'title' => __('Payment Method'),
            'required' => true,
            'value' => $this->_config->getStoreConfig('upslabel/pickup/PaymentMethod', $this->storeId),
            'values' => $this->paymentmethod->toOptionArray(),
        ]);

        $fieldset->addField('SpecialInstruction', 'textarea', [
            'name' => 'SpecialInstruction',
            'label' => __('Special Instruction'),
            'title' => __('Special Instruction'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/SpecialInstruction', $this->storeId),
        ]);

        $fieldset->addField('ReferenceNumber', 'textarea', [
            'name' => 'ReferenceNumber',
            'label' => __('Reference Number'),
            'title' => __('Reference Number'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/ReferenceNumber', $this->storeId),
        ]);

        $fieldset->addField('Notification', 'select', [
            'name' => 'Notification',
            'label' => __('Notification'),
            'title' => __('Notification'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/Notification', $this->storeId),
            'values' => $this->yesNo->toOptionArray(),
        ]);

        $fieldset->addField('ConfirmationEmailAddress', 'textarea', [
            'name' => 'ConfirmationEmailAddress',
            'label' => __('Confirmation Email Address'),
            'title' => __('Confirmation Email Address'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/ConfirmationEmailAddress', $this->storeId),
        ]);

        $fieldset->addField('UndeliverableEmailAddress', 'text', [
            'name' => 'UndeliverableEmailAddress',
            'label' => __('Undeliverable Email Address'),
            'title' => __('Undeliverable Email Address'),
            'value' => $this->_config->getStoreConfig('upslabel/pickup/UndeliverableEmailAddress', $this->storeId),
        ]);
        /*$fieldset->addField('status', 'hidden', array(
            'name'      => 'status',
        ));*/

        $fieldset->addField('ShipFrom', 'select', [
            'name' => 'ShipFrom',
            'label' => __('Ship From'),
            'title' => __('Ship From'),
            'required' => true,
            'value' => $this->_config->getStoreConfig('upslabel/shipping/defaultshipfrom', $this->storeId),
            'values' => $this->defaultAddress->toOptionArray(),
        ]);

        $fieldset->addField('OtherAddress', 'select', [
            'name' => 'oadress[OtherAddress]',
            'label' => __('Other address'),
            'title' => __('Other address'),
            'values' => $this->yesNo->toOptionArray(),
        ]);

        $fieldset->addField('companyname', 'text', [
            'name' => 'oadress[companyname]',
            'label' => __('Company name'),
            'title' => __('Company name'),
        ]);

        $fieldset->addField('attentionname', 'text', [
            'name' => 'oadress[attentionname]',
            'label' => __('Attention name'),
            'title' => __('Attention name'),
        ]);

        $fieldset->addField('phonenumber', 'text', [
            'name' => 'oadress[phonenumber]',
            'label' => __('Phone number'),
            'title' => __('Phone number'),
        ]);

        $fieldset->addField('addressline1', 'text', [
            'name' => 'oadress[addressline1]',
            'label' => __('Address'),
            'title' => __('Address'),
        ]);

        $fieldset->addField('room', 'text', [
            'name' => 'oadress[room]',
            'label' => __('Room'),
            'title' => __('Room'),
        ]);

        $fieldset->addField('floor', 'text', [
            'name' => 'oadress[floor]',
            'label' => __('Floor'),
            'title' => __('Floor'),
        ]);

        $fieldset->addField('city', 'text', [
            'name' => 'oadress[city]',
            'label' => __('City'),
            'title' => __('City'),
        ]);

        $fieldset->addField('stateprovincecode', 'text', [
            'name' => 'oadress[stateprovincecode]',
            'label' => __('State province code'),
            'title' => __('State province code'),
        ]);

        $fieldset->addField('urbanization', 'text', [
            'name' => 'oadress[urbanization]',
            'label' => __('Urbanization'),
            'title' => __('Urbanization'),
        ]);

        $fieldset->addField('postalcode', 'text', [
            'name' => 'oadress[postalcode]',
            'label' => __('Postal code'),
            'title' => __('Postal code'),
        ]);

        $fieldset->addField('countrycode', 'select', [
            'name' => 'oadress[countrycode]',
            'label' => __('Country'),
            'title' => __('Country'),
            'values' => $this->_countries->toOptionArray(),
        ]);

        $fieldset->addField('residential', 'select',
            [
                'name' => 'oadress[residential]',
                'label' => __('Residential'),
                'title' => __('Residential'),
                'values' => $this->residential->toOptionArray(),
            ]);

        $fieldset->addField('pickup_point', 'text',
            [
                'name' => 'oadress[pickup_point]',
                'label' => __('Pickup point'),
                'title' => __('Pickup point'),
            ]);
        $pickup_data = $this->_coreRegistry->registry('current_infomodus_upslabel_pickup');
        if ($pickup_data && count($pickup_data->getData()) > 0) {
            $fieldset->addField('pickup_request', 'textarea',
                [
                    'name' => 'pickup_request',
                    'readonly' => true,
                    'disabled' => true,
                    'style' => 'display:none;'
                ]);
            $fieldset->addField('pickup_response', 'textarea',
                [
                    'name' => 'pickup_response',
                    'readonly' => true,
                    'disabled' => true,
                    'style' => 'display:none;'
                ]);
            $fieldset->addField('pickup_cancel', 'textarea',
                [
                    'name' => 'pickup_cancel',
                    'readonly' => true,
                    'disabled' => true,
                    'style' => 'display:none;'
                ]);
            $fieldset->addField('pickup_cancel_request', 'textarea',
                [
                    'name' => 'pickup_cancel_request',
                    'readonly' => true,
                    'disabled' => true,
                    'style' => 'display:none;'
                ]);
        }
        if ($model->getId()) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
