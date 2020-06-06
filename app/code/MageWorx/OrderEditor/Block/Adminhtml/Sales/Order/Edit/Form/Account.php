<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;

class Account extends Template
{
    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Data\Form
     */
    protected $form;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $metadataFormFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Customer\Api\Data\OptionInterfaceFactory
     */
    protected $optionInterfaceFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Api\Data\OptionInterfaceFactory $optionInterfaceFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helperData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Model\Metadata\FormFactory $metadataFormFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Api\Data\OptionInterfaceFactory $optionInterfaceFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData             = $helperData;
        $this->formFactory            = $formFactory;
        $this->metadataFormFactory    = $metadataFormFactory;
        $this->dataObjectProcessor    = $dataObjectProcessor;
        $this->orderRepository        = $orderRepository;
        $this->optionInterfaceFactory = $optionInterfaceFactory;
    }

    /**
     * Return Form object
     *
     * @return \Magento\Framework\Data\Form
     */
    public function getForm()
    {
        if ($this->form === null) {
            $this->form = $this->formFactory->create();
            $this->prepareForm();
        }

        return $this->form;
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     */
    protected function prepareForm()
    {
        /** @var \Magento\Customer\Model\Metadata\Form $customerForm */
        $customerForm = $this->metadataFormFactory->create('customer', 'adminhtml_checkout');

        // prepare customer attributes to show
        $attributes = [];

        // add system required attributes
        foreach ($customerForm->getSystemAttributes() as $attribute) {
            if ($attribute->isRequired()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($customerForm->getUserAttributes() as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }

        $fieldset = $this->form->addFieldset('main', []);

        $fieldset->addField(
            'select_customer',
            'link',
            [
                'name'     => 'select_customer',
                'label'    => '',
                'class'    => '',
                'href'     => '#select_customer',
                'required' => '0'
            ]
        );

        $this->addAttributesToForm($attributes, $fieldset);

        $fieldset->addField(
            'customer_firstname',
            'text',
            [
                'name'     => 'customer_firstname',
                'label'    => __('Customer Firstname'),
                'class'    => '',
                'required' => '0',
            ]
        );

        $fieldset->addField(
            'customer_lastname',
            'text',
            [
                'name'     => 'customer_lastname',
                'label'    => __('Customer Lastname'),
                'class'    => '',
                'required' => '0',
            ]
        );

        $fieldset->addField(
            'customer_id',
            'hidden',
            [
                'name'     => 'customer_id',
                'label'    => '',
                'class'    => '',
                'required' => '0'
            ]
        );

        $this->form->addFieldNameSuffix('order[account]');
        $this->form->setValues($this->getFormValues());

        return $this;
    }

    /**
     * Return array of additional form element types by type
     *
     * @return array
     */
    protected function _getAdditionalFormElementTypes()
    {
        return [
            'file'    => \Magento\Customer\Block\Adminhtml\Form\Element\File::class,
            'image'   => \Magento\Customer\Block\Adminhtml\Form\Element\Image::class,
            'boolean' => \Magento\Customer\Block\Adminhtml\Form\Element\Boolean::class,
        ];
    }

    /**
     * Return array of additional form element renderers by element id
     *
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return [
            'region' => $this->getLayout()->createBlock(
                \Magento\Customer\Block\Adminhtml\Edit\Renderer\Region::class
            ),
        ];
    }

    /**
     * Add rendering EAV attributes to Form element
     *
     * @param \Magento\Customer\Api\Data\AttributeMetadataInterface[] $attributes
     * @param \Magento\Framework\Data\Form\AbstractForm $form
     * @return $this
     */
    protected function addAttributesToForm($attributes, \Magento\Framework\Data\Form\AbstractForm $form)
    {
        // add additional form element types
        $elementTypes = $this->_getAdditionalFormElementTypes();
        foreach ($elementTypes as $type => $className) {
            $form->addType($type, $className);
        }
        $elementRenderers = $this->_getAdditionalFormElementRenderers();

        foreach ($attributes as $attribute) {
            $inputType = $attribute->getFrontendInput();

            if ($inputType) {
                $element = $form->addField(
                    $attribute->getAttributeCode(),
                    $inputType,
                    [
                        'name'     => $attribute->getAttributeCode(),
                        'label'    => __($attribute->getStoreLabel()),
                        'class'    => $attribute->getFrontendClass(),
                        'required' => $attribute->isRequired(),
                    ]
                );
                if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
                /* add attribute to element */
                $element->setEntityAttribute($attribute);

                if (!empty($elementRenderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($elementRenderers[$attribute->getAttributeCode()]);
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $options          = [];
                    $attributeOptions = $attribute->getOptions();
                    if ($attribute->getAttributeCode() == 'group_id') {
                        /** @var \Magento\Customer\Api\Data\OptionInterface $notLoggedInGroup */
                        $notLoggedInGroup = $this->optionInterfaceFactory->create();
                        $notLoggedInGroup->setValue('0');
                        $notLoggedInGroup->setLabel('Not Logged In');
                        array_unshift($attributeOptions, $notLoggedInGroup);
                    }

                    foreach ($attributeOptions as $optionData) {
                        $data = $this->dataObjectProcessor->buildOutputDataArray(
                            $optionData,
                            \Magento\Customer\Api\Data\OptionInterface::class
                        );
                        foreach ($data as $key => $value) {
                            if (is_array($value)) {
                                unset($data[$key]);
                                $data['value'] = $value;
                            }
                        }
                        $options[] = $data;
                    }
                    $element->setValues($options);
                } elseif ($inputType == 'date') {
                    $format = $this->_localeDate->getDateFormat(
                        \IntlDateFormatter::SHORT
                    );
                    $element->setDateFormat($format);
                }
            }
        }

        return $this;
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        $order = $this->getOrder();

        return [
            'group_id'           => $order->getCustomerGroupId(),
            'customer_name'      => $order->getCustomerName(),
            'customer_firstname' => $order->getCustomerFirstname(),
            'customer_lastname'  => $order->getCustomerLastname(),
            'email'              => $order->getCustomerEmail(),
            'select_customer'    => __('Select a Customer'),
            'customer_id'        => $order->getCustomerId()
        ];
    }

    /**
     * Return "Submit" button html
     *
     * @return string
     */
    public function getSubmitButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                     ->setData(
                         [
                             'id'    => 'account-submit',
                             'label' => __('Submit'),
                             'type'  => 'button',
                             'class' => 'edit primary',
                             'style' => 'margin-top: 1em; float:right;',
                         ]
                     )
                     ->toHtml();

        return $html;
    }

    /**
     * Return "Cancel" button html
     *
     * @return string
     */
    public function getCancelButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                     ->setData(
                         [
                             'label' => __('Cancel'),
                             'id'    => 'account-cancel',
                             'class' => 'edit primary',
                             'type'  => 'button',
                             'style' => 'margin-top: 1em; float:left;',
                         ]
                     )
                     ->toHtml();

        return $html;
    }

    /**
     * @param Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get Quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        $order = $this->helperData->getOrder();
        if (!$order) {
            $orderId = $this->getRequest()->getParam('order_id');
            $order   = $this->orderRepository->get($orderId);
        }

        return $order;
    }

    /**
     * Notice message
     *
     * @return \Magento\Framework\Phrase
     */
    public function getNotice()
    {
        return __(
            'Note that changing the customer\'s details won\'t recalculate the taxes and discounts. 
            You should adjust the prices manually if necessary.'
        );
    }
}