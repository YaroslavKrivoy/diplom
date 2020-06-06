<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Framework\Model\AbstractModel;

class Payment extends AbstractModel
{
    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $paymentComment;

    /**
     * @var string
     */
    protected $paymentMethod;

    /**
     * @var string
     */
    protected $paymentTitle;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Authorizenet\Model\Directpost\Response
     */
    protected $response;

    /**
     * Payment constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param Order $order
     * @param Quote $quote
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Authorizenet\Model\Directpost\Response $response
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        Order $order,
        Quote $quote,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Authorizenet\Model\Directpost\Response $response,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->order           = $order;
        $this->quote           = $quote;
        $this->directoryHelper = $directoryHelper;
        $this->helperData      = $helperData;
        $this->response        = $response;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

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
     * @return string
     */
    public function getPaymentComment()
    {
        return $this->paymentComment;
    }

    /**
     * @param string $paymentComment
     * @return $this
     */
    public function setPaymentComment($paymentComment)
    {
        $this->paymentComment = $paymentComment;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentTitle()
    {
        return $this->paymentTitle;
    }

    /**
     * @param string $paymentTitle
     * @return $this
     */
    public function setPaymentTitle($paymentTitle)
    {
        $this->paymentTitle = $paymentTitle;

        return $this;
    }

    /**
     * @param string $paymentMethod
     */
    protected function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Init param
     *
     * @param array $params
     */
    public function initParams($params)
    {
        if (isset($params['order_id'])) {
            $this->setOrderId($params['order_id']);
        }
        if (isset($params['payment_method'])) {
            $this->setPaymentMethod($params['payment_method']);
        }
        if (isset($params['payment_title'])) {
            $this->setPaymentTitle($params['payment_title']);
        }
    }

    /**
     * Update payment method
     *
     * @throws \Exception
     */
    public function updatePaymentMethod()
    {
        $this->loadOrder();
        $payment = $this->order->getPayment();
        $payment->setMethod($this->paymentMethod);
        /* Prepare date for additional information */
        if ($this->paymentTitle !== null) {
            $payment->setAdditionalInformation('method_title', $this->paymentTitle);
        }

        $this->order
            ->setPayment($payment)
            ->save();

        /* change data in quote */
        $quote   = $this->getQuote();
        $payment = $quote->getPayment();
        $payment->setMethod($this->paymentMethod);
        if ($this->paymentTitle !== null) {
            $payment->setAdditionalInformation('method_title', $this->paymentTitle);
        }
    }

    /**
     * @return Order
     * @throws \Exception
     */
    protected function loadOrder()
    {
        $id = $this->getOrderId();
        $this->order->load($id);
        if (!$this->order->getEntityId()) {
            throw new \Exception('Can not load order');
        }

        return $this->order;
    }

    /**
     * @return $this
     */
    protected function getQuote()
    {
        return $this->quote->load($this->order->getQuoteId());
    }
}