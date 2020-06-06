<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment;

use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method\Form as PaymentMethodForm;
use MageWorx\OrderEditor\Model\Ui\ConfigProvider;

class Method extends PaymentMethodForm
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
     * @var \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment
     */
    protected $payment;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Payment\Model\MethodList
     */
    private $methodList;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Payment\Api\PaymentMethodListInterface
     */
    private $paymentMethodList;

    /**
     * @var \Magento\Payment\Model\Method\InstanceFactory
     */
    private $paymentMethodInstanceFactory;

    /**
     * Method constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment $payment
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param \Magento\Payment\Model\MethodList $methodList
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList
     * @param \Magento\Payment\Model\Method\InstanceFactory $paymentMethodInstanceFactory
     * @param array $data
     * @param array $additionalChecks
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Checks\SpecificationFactory $methodSpecificationFactory,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Payment $payment,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Payment\Model\MethodList $methodList,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList,
        \Magento\Payment\Model\Method\InstanceFactory $paymentMethodInstanceFactory,
        array $data = [],
        array $additionalChecks = []
    ) {
        $this->payment                      = $payment;
        $this->paymentHelper                = $paymentHelper;
        $this->helperData                   = $helperData;
        $this->methodList                   = $methodList;
        $this->moduleList                   = $moduleList;
        $this->paymentMethodList            = $paymentMethodList;
        $this->paymentMethodInstanceFactory = $paymentMethodInstanceFactory;

        $moduleInfo = $this->moduleList->getOne('Magento_Sales');
        $version    = $moduleInfo['setup_version'];

        if (version_compare($version, '2.0.7', '<')) {
            parent::__construct(
                $context,
                $paymentHelper,
                $methodSpecificationFactory,
                $sessionQuote,
                $data
            );
        } else {
            parent::__construct(
                $context,
                $paymentHelper,
                $methodSpecificationFactory,
                $sessionQuote,
                $data,
                $additionalChecks
            );
        }


    }

    public function setPaymentMethod()
    {
        $quote = $this->getQuote();
        $this->setData('methods', $this->methodList->getAvailableMethods($quote));
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
     * @return Quote
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
        return $this->order;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getPaymentTitle($code)
    {
        if ($code == ConfigProvider::CODE) {
            $quote = $this->getQuote();
            if ($quote == null) {
                return "";
            }
            $payment        = $quote->getPayment();
            $additionalInfo = $payment->getAdditionalInformation();
            if (!empty($additionalInfo["method_title"])) {
                return $additionalInfo["method_title"];
            }
        }

        return "";
    }

    /**
     * Get offline payment method
     *
     * @return array|mixed|null
     */
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if ($methods === null) {
            $quote   = $this->getQuote();
            $store   = $quote ? $quote->getStoreId() : null;
            $methods = [];
            foreach ($this->paymentMethodList->getActiveList($store) as $method) {
                $methodInstance = $this->paymentMethodInstanceFactory->create($method);
                if ($methodInstance->isAvailable($quote)
                    && $this->_canUseMethod($methodInstance)
//                    && $methodInstance->isOffline()
                ) {
                    $this->_assignMethod($methodInstance);
                    $methods[] = $methodInstance;
                }
            }
            $this->setData('methods', $methods);
        }

        return $methods;
    }
}