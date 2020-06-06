<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin\Block\Sales\Adminhtml\Order\View;

class Payment
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * Payment constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order $order
    ) {
        $this->orderRepository = $orderRepository;
        $this->order           = $order;
    }

    /**
     * @param \MageWorx\OrderEditor\Plugin\Block\Sales\Adminhtml\Order\View\Payment $subject
     * @param string $result
     * @return mixed
     * @throws \Exception
     */
    public function afterGetTitle($subject, $result)
    {
        if ($subject->getCode() == 'mageworx_ordereditor_payment_method') {
            $instance = $subject->getInfoInstance();
            if (is_null($instance)) return $result;
            $orderId  = $instance->getParentId();
            if ($orderId != null) {
                $this->loadOrder($orderId);
                $payment          = $this->order->getPayment();
                $orderInformation = $payment->getAdditionalInformation();
                if (!empty($orderInformation['method_title'])) {
                    return $orderInformation['method_title'];
                }
            }
        }

        return $result;
    }

    /**
     * @param int $orderId
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    protected function loadOrder($orderId)
    {
        $this->order->load($orderId);
        if (!$this->order->getEntityId()) {
            throw new \Exception('Can not load order');
        }

        return $this->order;
    }
}