<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Transparent;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Config;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use Magento\Payment\Model\MethodInterface;

class PaymentForm extends \Magento\Payment\Block\Adminhtml\Transparent\Form
{
    protected $_template = 'MageWorx_OrderEditor::directpost/info.phtml';

    /**
     * @var \Magento\Payment\Api\PaymentMethodListInterface
     */
    private $paymentMethodList;

    /**
     * @var \Magento\Payment\Model\Method\InstanceFactory
     */
    private $paymentMethodInstanceFactory;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * PaymentForm constructor.
     *
     * @param Context $context
     * @param Config $paymentConfig
     * @param Session $checkoutSession
     * @param \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory
     * @param \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment $payment
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList
     * @param \Magento\Payment\Model\Method\InstanceFactory $paymentMethodInstanceFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $paymentConfig,
        Session $checkoutSession,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
        \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment $payment,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList,
        \Magento\Payment\Model\Method\InstanceFactory $paymentMethodInstanceFactory,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $checkoutSession, $data);
        $this->paymentMethodList            = $paymentMethodList;
        $this->paymentMethodInstanceFactory = $paymentMethodInstanceFactory;
        $this->helperData                   = $helperData;
    }

    /**
     * Sets payment method instance to form
     *
     * @param MethodInterface $method
     * @return $this
     */
    public function setMethod(MethodInterface $method)
    {
        $this->setData('method', $method);

        return $this;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMethodCode()
    {
        return $this->getMethod()->getCode();
    }

    /**
     * @param string $field
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInfoData($field)
    {
        return $this->escapeHtml($this->getMethod()->getInfoInstance()->getData($field));
    }

    /**
     * @param \Magento\Payment\Model\MethodInterface $method
     * @return $this
     */
    protected function assignMethod($method)
    {
        $method->setInfoInstance($this->getQuote()->getPayment());

        return $this;
    }

    /**
     * @return MethodInterface
     */
    public function getMethod()
    {
        $method = $this->getData('method');
        if ($method === null) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;

            foreach ($this->paymentMethodList->getActiveList($store) as $method) {
                if ($method->getCode() == 'authorizenet_directpost') {
                    $methodInstance = $this->paymentMethodInstanceFactory->create($method);
                    if ($methodInstance->isAvailable($quote)) {
                        $method = $methodInstance;
                        $this->assignMethod($methodInstance);
                        $this->setData('method', $method);

                        return $method;
                    }
                }
            }
        } else {
            $this->assignMethod($method);
        }

        return $method;
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
    }
}