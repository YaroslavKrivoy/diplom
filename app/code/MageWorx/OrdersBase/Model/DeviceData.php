<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersBase\Model;

use Magento\Framework\Model\AbstractModel;
use MageWorx\OrdersBase\Api\Data\DeviceDataInterface;

class DeviceData extends AbstractModel implements DeviceDataInterface
{
    const TABLE_NAME = 'mageworx_order_base_device_data';

    const AREA_UNKNOWN = 0;
    const AREA_FRONT = 1;
    const AREA_ADMIN = 2;
    const AREA_REST = 3;
    const AREA_SOAP = 4;

    /**
     * Returns array of all available area codes
     *
     * @return array
     */
    public static function getAreaCodes()
    {
        return [
            static::AREA_UNKNOWN => 'unknown',
            static::AREA_FRONT => 'frontend',
            static::AREA_ADMIN => 'admin',
            static::AREA_REST => 'rest api',
            static::AREA_SOAP => 'soap api',
        ];
    }

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\OrdersBase\Model\ResourceModel\DeviceData');
        $this->setIdFieldName('entity_id');
    }

    /**
     * Get device code (numeric)
     *
     * @see \DeviceDetector\Parser\Device\DeviceParserAbstract::$deviceTypes
     *
     * @return int
     */
    public function getDeviceCode()
    {
        return $this->getData('device_code');
    }

    /**
     * Get area code from where the order was placed
     *
     * @see \MageWorx\OrdersBase\Model\DeviceData::getAreaCodes()
     *
     * @return mixed
     */
    public function getAreaCode()
    {
        return $this->getData('area_code');
    }

    /**
     * Set used device code
     *
     * @param int $code
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setDeviceCode($code)
    {
        return $this->setData('device_code', $code);
    }

    /**
     * Set used area code from where the order was placed
     *
     * @param int $code
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setAreaCode($code)
    {
        return $this->setData('area_code', $code);
    }

    /**
     * Get linked order id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Set linked order id
     *
     * @param int $id
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setOrderId($id)
    {
        return $this->setData('order_id', $id);
    }

    /**
     * Return value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->getData();
    }

    /**
     * Set value.
     *
     * @param array $value
     * @return \MageWorx\OrdersBase\Api\Data\DeviceDataInterface
     */
    public function setValue($value = [])
    {
        return $this->addData($value);
    }

    /**
     * Get human readable device name
     *
     * @return string
     */
    public function getDeviceName()
    {
        $code = $this->getDeviceCode();
        $name = \DeviceDetector\Parser\Device\DeviceParserAbstract::getDeviceName($code);
        $humanReadableName = ucwords($name);

        return $humanReadableName;
    }

    /**
     * Get human readable area name (from where order was placed)
     *
     * @return string
     */
    public function getAreaName()
    {
        $code = $this->getAreaCode();
        $codes = $this->getAreaCodes();
        $name = !empty($codes[$code]) ? $codes[$code] : __('Empty');
        $humanReadableName = ucwords($name);

        return $humanReadableName;
    }
}
