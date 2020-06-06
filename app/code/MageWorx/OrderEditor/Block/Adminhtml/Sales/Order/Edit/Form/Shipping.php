<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form;

use Magento\Backend\Block\Template;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Result\PageFactory;

class Shipping extends Template
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
     * @var ShippingModel
     */
    protected $shipping;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ShippingModel $shipping
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ShippingModel $shipping,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shipping = $shipping;
        $this->helperData = $helperData;
    }

    /**
     * @return ShippingModel
     */
    public function getShipping()
    {
        $this->shipping->setQuote($this->getQuote());
        return $this->shipping;
    }

    /**
     * @param ShippingModel $shipping
     * @return $this
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
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

    protected function reloadShippingRates()
    {
        $this->getShipping()->setOrder($this->getOrder());
        $this->getShipping()->reloadShippingRates();
    }

    /**
     * @return string
     */
    public function getShippingForm()
    {
        $this->reloadShippingRates();

        /**
         * @var \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Shipping\Method $shippingMethodForm
         */
        $shippingMethodForm = $this->getChildBlock('shipping_method');

        if ($shippingMethodForm) {
            $shippingMethodForm->setQuote($this->getQuote());
            $shippingMethodForm->setOrder($this->getOrder());

            return $shippingMethodForm->toHtml();
        }

        return '';
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
                    'id' => 'shipping-method-submit',
                    'label' => __('Submit'),
                    'type' => 'button',
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
                    'id' => 'shipping-method-cancel',
                    'label' => __('Cancel'),
                    'type' => 'button',
                    'class' => 'edit primary',
                    'style' => 'margin-top: 1em; float:left;',
                ]
            )
            ->toHtml();

        return $html;
    }
}
