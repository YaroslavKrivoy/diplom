<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersBase\Api;

use MageWorx\OrdersBase\Model\DeviceData;
use Magento\Framework\Api\SearchCriteriaInterface;

interface DeviceDataRepositoryInterface
{
    /**
     * Save device data.
     *
     * @param DeviceData $deviceData
     * @return DeviceData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DeviceData $deviceData);

    /**
     * Retrieve device data.
     *
     * @param int $deviceDataId
     * @return DeviceData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($deviceDataId);

    /**
     * Retrieve device data.
     *
     * @param int $orderId
     * @return DeviceData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByOrderId($orderId);

    /**
     * Retrieve device data matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $returnRawObjects = false);

    /**
     * Delete device data.
     *
     * @param DeviceData $deviceData
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DeviceData $deviceData);

    /**
     * Delete device data by ID.
     *
     * @param int $deviceDataId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($deviceDataId);

    /**
     * Get empty DeviceData
     *
     * @return DeviceData|Data\DeviceDataInterface
     */
    public function getEmptyEntity();
}
