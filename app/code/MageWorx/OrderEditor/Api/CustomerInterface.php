<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Api;

/**
 * Interface CustomerInterface
 * @package MageWorx\OrderEditor\Api
 *
 * Formally depends on the currently editing order and data which transferred from the account edit form
 */
interface CustomerInterface
{
    /**
     * Performs update of the customer's records in the corresponding order
     *
     * @see \MageWorx\OrderEditor\Model\Customer::update()
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update();

    /**
     * Set the corresponding order id (which will be modified)
     *
     * @see \MageWorx\OrderEditor\Model\Customer::setOrderId()
     * @param int $orderId
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     */
    public function setOrderId($orderId);

    /**
     * Get the corresponding order id (which will be modified)
     *
     * @see \MageWorx\OrderEditor\Model\Customer::getOrderId()
     * @return int
     */
    public function getOrderId();

    /**
     * Set a customer data which will replace the order's customer records during update
     *
     * @see \MageWorx\OrderEditor\Model\Customer::setCustomerData()
     * @param array $data
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     */
    public function setCustomerData(array $data = []);

    /**
     * Get the customer data which will replace the order's customer records during update
     *
     * @see \MageWorx\OrderEditor\Model\Customer::getCustomerData()
     * @return \Magento\Framework\DataObject
     */
    public function getCustomerData();

    /**
     * Set the customer id. In case it is exists, the corresponding order's customer id will be replaced to this one.
     *
     * @see \MageWorx\OrderEditor\Model\Customer::setCustomerId()
     * @param int|null $customerId
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     */
    public function setCustomerId($customerId = null);

    /**
     * Get the id of a customer, which has been associated with a current order.
     *
     * @see \MageWorx\OrderEditor\Model\Customer::getCustomerId()
     * @return int
     */
    public function getCustomerId();
}
