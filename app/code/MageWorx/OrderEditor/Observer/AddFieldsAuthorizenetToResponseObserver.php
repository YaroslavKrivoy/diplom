<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class AddFieldsAuthorizenetToResponseObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Authorizenet\Model\Directpost
     */
    protected $payment;

    /**
     * @var \Magento\Authorizenet\Model\Directpost\Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Authorizenet\Model\Directpost $payment
     * @param \Magento\Authorizenet\Model\Directpost\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Authorizenet\Model\Directpost $payment,
        \Magento\Authorizenet\Model\Directpost\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        \Magento\Sales\Model\Order $order
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->payment      = $payment;
        $this->session      = $session;
        $this->storeManager = $storeManager;
        $this->helperData   = $helperData;
        $this->order        = $order;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $this->getOrder($observer);
        if (!$order || !$order->getId()) {
            return $this;
        }

        $payment = $order->getPayment();
        if (!$payment || $payment->getMethod() != $this->payment->getCode()) {
            return $this;
        }

        $result = $observer->getData('result')->getData();
        if (!empty($result['error'])) {
            return $this;
        }

        // if success, then set order to session and add new fields
        $requestToAuthorizenet = $payment->getMethodInstance()
                                         ->generateRequestFromOrder($order);
        $requestToAuthorizenet->setControllerActionName(
            $observer->getData('action')
                     ->getRequest()
                     ->getControllerName()
        );
        $requestToAuthorizenet->setIsSecure(
            (string)$this->storeManager->getStore()
                                       ->isCurrentlySecure()
        );

        $result[$this->payment->getCode()] = ['fields' => $requestToAuthorizenet->getData()];

        $observer->getData('result')->setData($result);

        return $this;
    }

    /**
     * Get Order
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return $this|Order
     */
    protected function getOrder($observer)
    {
        /* @var $order Order */
        $order = $this->coreRegistry->registry('ordereditor_order');
        if (!$order || !$order->getId()) {
            $result = $observer->getData('result')->getData();

            if (!empty($result['order_id'])) {
                $orderId = $result['order_id'];
                $order   = $this->order->load($orderId);
            }
        }

        return $order;
    }
}