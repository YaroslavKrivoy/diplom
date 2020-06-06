<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form;

use Magento\Backend\Block\Template;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;
use Magento\Backend\Block\Template\Context;

class Payment extends Template
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
     * @var PaymentModel
     */
    protected $payment;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Payment constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
    }

    /**
     * @return PaymentModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPayment()
    {
        $this->payment->setQuote($this->getQuote());

        return $this->payment;
    }

    /**
     * @param PaymentModel $payment
     * @return $this
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return Quote
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
        return $this->helperData->getOrder();
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
     * @return string
     */
    public function getPaymentForm()
    {
        /**
         * @var \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment\Method $paymentMethodForm
         */
        $paymentMethodForm = $this->getChildBlock('payment_method');

        if ($paymentMethodForm) {
            $paymentMethodForm->setQuote($this->getQuote());
            $paymentMethodForm->setOrder($this->getOrder());

            return $paymentMethodForm->toHtml();
        }

        return '';
    }

    /**
     * Return "Submit" button html
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubmitButtonHtml()
    {
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                     ->setData(
                         [
                             'id'    => 'payment-method-submit',
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
                             'id'    => 'payment-method-cancel',
                             'label' => __('Cancel'),
                             'type'  => 'button',
                             'class' => 'edit primary',
                             'style' => 'margin-top: 1em; float:left;',
                         ]
                     )
                     ->toHtml();

        return $html;
    }
}