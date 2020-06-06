<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Controller\Adminhtml\Edit;

use MageWorx\OrderEditor\Controller\Adminhtml\AbstractAction;
use MageWorx\OrderEditor\Helper\Data;
use MageWorx\OrderEditor\Model\Order;
use MageWorx\OrderEditor\Model\Quote;
use MageWorx\OrderEditor\Model\Shipping as ShippingModel;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use MageWorx\OrderEditor\Model\Payment as PaymentModel;

class Account extends AbstractAction
{
    /**
     * @var \MageWorx\OrderEditor\Model\Customer
     */
    protected $customer;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RawFactory $resultFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param Quote $quote
     * @param Order $order
     * @param ShippingModel $shipping
     * @param PaymentModel $payment
     * @param \MageWorx\OrderEditor\Model\Customer $customer
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        Quote $quote,
        Order $order,
        ShippingModel $shipping,
        PaymentModel $payment,
        \MageWorx\OrderEditor\Model\Customer $customer
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultFactory,
            $helper,
            $scopeConfig,
            $quote,
            $order,
            $shipping,
            $payment
        );
        $this->customer = $customer;
    }

    /**
     * @inheritdoc
     *
     * @return void
     * @throws \Exception
     */
    protected function update()
    {
        $order        = $this->loadOrder();
        $customerId   = $this->getCustomerId();
        $customerData = $this->getCustomerData();

        $this->customer
            ->setOrderId($order->getId())
            ->setCustomerId($customerId)
            ->setCustomerData($customerData)
            ->update();
    }

    /**
     * Get customer id from request if specified
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        $orderData = $this->getRequest()->getParam('order');
        if (!empty($orderData['account']['customer_id'])) {
            return (int)$orderData['account']['customer_id'];
        }

        return null;
    }

    /**
     * Get customer's data from a request if specified
     *
     * @return array
     */
    protected function getCustomerData()
    {
        $orderData = $this->getRequest()->getParam('order');
        if (!empty($orderData['account'])) {
            return $orderData['account'];
        }

        return [];
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function prepareResponse()
    {
        return 'reload';
    }
}
