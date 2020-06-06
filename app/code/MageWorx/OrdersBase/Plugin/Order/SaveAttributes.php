<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersBase\Plugin\Order;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SaveAttributes
{
    /**
     * @var \MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface
     */
    protected $deviceDataRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * SaveAttributes constructor.
     * @param \MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface $deviceDataRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface $deviceDataRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->deviceDataRepository = $deviceDataRepository;
        $this->logger = $logger;
    }

    /**
     * Save extension attributes
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $resultOrder
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {
        $resultOrder = $this->saveDeviceData($resultOrder);

        return $resultOrder;
    }

    /**
     * Save device data attributes
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws CouldNotSaveException
     */
    private function saveDeviceData(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /** @var \Magento\Sales\Api\Data\OrderExtension $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if (null !== $extensionAttributes && null !== $extensionAttributes->getDeviceData()) {
            $attributeValue = $extensionAttributes->getDeviceData()->getValue();

            // Get device data entity
            try {
                $deviceData = $this->deviceDataRepository->getByOrderId($order->getEntityId());
            } catch (NoSuchEntityException $e) {
                $deviceData = $this->deviceDataRepository->getEmptyEntity();
            }

            // Update|Save new data
            try {
                $deviceData->setValue($attributeValue);
                $deviceData->setOrderId($order->getEntityId());
                $this->deviceDataRepository->save($deviceData);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $order;
    }
}
