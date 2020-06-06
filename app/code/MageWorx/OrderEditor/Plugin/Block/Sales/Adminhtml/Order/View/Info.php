<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Plugin\Block\Sales\Adminhtml\Order\View;

class Info
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Info constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param array                                          $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetCustomerAccountData($subject, $result)
    {
        $order            = $subject->getOrder();
        $deviceDataObject = $this->getDeviceDataObject($order);
        if ($deviceDataObject) {
            $result[] = [
                'label' => __('Device'),
                'value' => $deviceDataObject->getDeviceName()
            ];
            $result[] = [
                'label' => __('Area'),
                'value' => $deviceDataObject->getAreaName()
            ];
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    protected function getDeviceDataObject($order)
    {
        /** @var \Magento\Sales\Api\Data\OrderExtension $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes || !$extensionAttributes instanceof \Magento\Sales\Api\Data\OrderExtension) {
            $order = $this->orderRepository->get($order->getId());
            /** @var \Magento\Sales\Api\Data\OrderExtension $extensionAttributes */
            $extensionAttributes = $order->getExtensionAttributes();
        }

        if (!$extensionAttributes) {
            return null;
        }

        return $extensionAttributes->getDeviceData();
    }
}