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

class Info extends Template
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
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Config\Source\Order\Status
     */
    protected $orderStatusConfig;

    /**
     * @var \MageWorx\OrderEditor\Model\Config\Source\Order\State
     */
    protected $orderStateConfig;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $helperData
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Config\Source\Order\Status $orderStatusConfig
     * @param \MageWorx\OrderEditor\Model\Config\Source\Order\State $orderStateConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $helperData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Config\Source\Order\Status $orderStatusConfig,
        \MageWorx\OrderEditor\Model\Config\Source\Order\State $orderStateConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData        = $helperData;
        $this->formFactory       = $formFactory;
        $this->orderRepository   = $orderRepository;
        $this->orderStatusConfig = $orderStatusConfig;
        $this->orderStateConfig  = $orderStateConfig;
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
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
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
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     */
    protected function prepareForm()
    {
        $fieldset = $this->form->addFieldset('main', []);

        /**
         * Temporally removed because increasing of the increment id leads to
         * fatal error during future checkout
         */
//        $fieldset->addField(
//            'increment_id',
//            'text',
//            [
//                'name' => 'increment_id',
//                'label' => 'Order #',
//                'class' => 'increment_id',
//                'required' => true
//            ]
//        );

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $timeFormat = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);
        $fieldset->addType('custom_date', 'MageWorx\OrderEditor\Block\Adminhtml\Form\Element\CustomDate');
        $fieldset->addField(
            'created_at',
            'custom_date',
            [
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT,
                'name'         => 'created_at',
                'label'        => __('Order Date'),
                'title'        => __('Order Date'),
                'required'     => true,
                'date_format'  => $dateFormat,
                'time_format'  => $timeFormat
            ]
        );

        $statusOptions = $this->orderStatusConfig->toOptionArray();
        array_shift($statusOptions);
        $fieldset->addField(
            'status',
            'select',
            [
                'name'     => 'status',
                'label'    => __('Status'),
                'title'    => __('Status'),
                'values'   => $statusOptions,
                'required' => true,
            ]
        );

        $fieldset->addField(
            'state',
            'select',
            [
                'name'     => 'state',
                'label'    => __('State'),
                'title'    => __('State'),
                'values'   => $this->orderStateConfig->toOptionArray(),
                'required' => true,
            ]
        );

        $this->form->addFieldNameSuffix('order[info]');
        $this->form->setValues($this->getFormValues());

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
            'increment_id' => $order->getIncrementId(),
            'created_at'   => $order->getCreatedAt(),
            'status'       => $order->getStatus(),
            'state'        => $order->getState(),
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
                             'id'    => 'info-submit',
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
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCancelButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                     ->setData(
                         [
                             'label' => __('Cancel'),
                             'id'    => 'info-cancel',
                             'class' => 'edit primary',
                             'type'  => 'button',
                             'style' => 'margin-top: 1em; float:left;',
                         ]
                     )
                     ->toHtml();

        return $html;
    }

    /**
     * Notice message
     *
     * @return \Magento\Framework\Phrase
     */
    public function getNotice()
    {
        return __(
            'Note that changing the order\'s details won\'t recalculate the taxes and discounts. 
            You should adjust the prices manually if necessary.'
        );
    }
}