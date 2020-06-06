<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersBase\Plugin\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface;
use Psr\Log\LoggerInterface;

class GetAttributes
{
    /**
     * @var DeviceDataRepositoryInterface
     */
    protected $deviceDataRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * SaveAttributes constructor.
     * @param DeviceDataRepositoryInterface $deviceDataRepository
     * @param LoggerInterface $logger
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        DeviceDataRepositoryInterface $deviceDataRepository,
        LoggerInterface $logger,
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->deviceDataRepository = $deviceDataRepository;
        $this->logger = $logger;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Add extension attributes to the order
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $resultOrder
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {
        $resultOrder = $this->addDeviceDataToOrder($resultOrder);

        return $resultOrder;
    }

    /**
     * Add extension attributes (device data) to the order storage
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function addDeviceDataToOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {

        try {
            $attributeValue = $this->deviceDataRepository->getByOrderId($order->getEntityId());
        } catch (NoSuchEntityException $e) {
            return $order;
        }

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $orderExtension->setDeviceData($attributeValue);
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }
}
