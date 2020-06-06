<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model;

class OrderItemBox extends \Magento\Framework\Model\AbstractModel implements \Aitoc\DimensionalShipping\Api\Data\OrderItemBoxInterface
{

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox');
    }
    
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
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBoxId()
    {
        return $this->getData(self::BOX_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderBoxId($boxId)
    {
        return $this->setData(self::BOX_ID, $boxId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparate()
    {
        return $this->getData(self::SEPARATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSeparate($value)
    {
        return $this->setData(self::SEPARATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
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
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
         return $this->setData(self::SKU, $sku);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return $this->getData(self::ERROR_MESSAGE);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setErrorMessage($message)
    {
        return $this->setData(self::ERROR_MESSAGE, $message);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getNotPacked()
    {
        return $this->getData(self::NOT_PACKED);
    }
    
    /**
     * {@inheritdoc}
     */
    public function setNotPacked($notPacked)
    {
        return $this->setData(self::NOT_PACKED, $notPacked);
    }
}
