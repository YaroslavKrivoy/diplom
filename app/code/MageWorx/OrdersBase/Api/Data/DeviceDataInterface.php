<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrdersBase\Api\Data;

interface DeviceDataInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * Get device code (numeric)
     *
     * @see \DeviceDetector\Parser\Device\DeviceParserAbstract::$deviceTypes
     *
     * @return int
     */
    public function getDeviceCode();

    /**
     * Get area code from where the order was placed
     *
     * @see \MageWorx\OrdersBase\Model\DeviceData::getAreaCodes()
     *
     * @return mixed
     */
    public function getAreaCode();

    /**
     * Get linked order id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Return value.
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Get human readable device name
     *
     * @return string
     */
    public function getDeviceName();

    /**
     * Get human readable area name (from where order was placed)
     *
     * @return string
     */
    public function getAreaName();

    /**
     * @param $id
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setEntityId($id);

    /**
     * Set used device code
     *
     * @param int $code
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setDeviceCode($code);

    /**
     * Set used area code from where the order was placed
     *
     * @param int $code
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setAreaCode($code);

    /**
     * Set linked order id
     *
     * @param int $id
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setOrderId($id);

    /**
     * Set value.
     *
     * @param array $value
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setValue($value = []);
}
