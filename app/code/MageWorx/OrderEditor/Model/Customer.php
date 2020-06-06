<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model;

use Magento\Framework\Exception\LocalizedException;

class Customer extends \Magento\Framework\DataObject implements \MageWorx\OrderEditor\Api\CustomerInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var array
     */
    protected $dataMap = [
        'customer_group_id' => 'group_id',
        'customer_firstname' => 'customer_firstname',
        'customer_lastname' => 'customer_lastname',
        'customer_email' => 'email',
        'customer_id' => 'customer_id',
    ];

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $dataObjectFactory;

    /**
     * Customer constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\DataObject\Factory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->orderRepository = $orderRepository;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Performs update of the customer's records in the corresponding order
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        $customerData = $this->getCustomerData();
        $orderHasChanges = false;
        foreach ($this->getDataMap() as $field => $dep) {
            $newData = $customerData->getData($dep);
            if ($dep == 'customer_id' && !$newData) {
                continue;
            } elseif ($dep == 'customer_id' && $newData) {
                $order->setCustomerIsGuest(false);
            }
            if ($newData !== null) {
                $order->setData($field, $newData);
                $orderHasChanges = true;
            }
        }

        if ($orderHasChanges) {
            $this->orderRepository->save($order);
        }

        return $this;
    }

    /**
     * Set the corresponding order id (which will be modified)
     *
     * @param int $orderId
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * Get the corresponding order id (which will be modified)
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * Set a customer data which will replace the order's customer records during update
     *
     * @param array $data
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     */
    public function setCustomerData(array $data = [])
    {
        $data = $this->dataObjectFactory->create($data);

        return $this->setData('customer_data', $data);
    }

    /**
     * Get the customer data which will replace the order's customer records during update
     *
     * @return \Magento\Framework\DataObject
     */
    public function getCustomerData()
    {
        $data = $this->getData('customer_data') ? $this->getData('customer_data') : [];
        if (!$data instanceof \Magento\Framework\DataObject) {
            $data = $this->dataObjectFactory->create($data);
        }

        return $data;
    }

    /**
     * Set the customer id. In case it is exists, the corresponding order's customer id will be replaced to this one.
     *
     * @param int|null $customerId
     * @return \MageWorx\OrderEditor\Api\CustomerInterface
     */
    public function setCustomerId($customerId = null)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * Get the id of a customer, which has been associated with a current order.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws LocalizedException
     */
    private function getOrder()
    {
        if (!$this->getOrderId()) {
            throw new LocalizedException(__('To perform update the order id should be specified'));
        }

        $orderId = $this->getOrderId();
        $order = $this->orderRepository->get($orderId);

        return $order;
    }

    /**
     * Get data map which using to set form data to the corresponding order field
     *
     * @return array
     */
    private function getDataMap()
    {
        return $this->dataMap;
    }
}
