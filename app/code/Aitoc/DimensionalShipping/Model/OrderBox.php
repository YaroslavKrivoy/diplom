<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model;

class OrderBox extends \Magento\Framework\Model\AbstractModel implements \Aitoc\DimensionalShipping\Api\Data\OrderBoxInterface
{

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ENTITY_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getBoxId()
    {
        return $this->getData(self::BOX_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setBoxId($boxId)
    {
        return $this->setData(self::BOX_ID, $boxId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox');
    }
}
