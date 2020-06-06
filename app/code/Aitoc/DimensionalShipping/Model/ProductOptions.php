<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model;

class ProductOptions extends \Magento\Framework\Model\AbstractModel implements \Aitoc\DimensionalShipping\Api\Data\ProductOptionsInterface
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
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectBox()
    {
        return $this->getData(self::SELECT_BOX);
    }

    /**
     * {@inheritdoc}
     */
    public function setSelectBox($selectBox)
    {
        return $this->setData(self::SELECT_BOX, $selectBox);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight($height)
    {
        return $this->setData(self::HEIGHT, $height);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setWidth($width)
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->getData(self::LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setLength($length)
    {
        return $this->setData(self::LENGTH, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialBox()
    {
        return $this->getData(self::SPECIAL_BOX);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecialBox($specialBox)
    {
        return $this->setData(self::SPECIAL_BOX, $specialBox);
    }

    /**
     * {@inheritdoc}
     */
    public function getPackSeparately()
    {
        return $this->getData(self::PACK_SEPARATELY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPackSeparately($packSeparately)
    {
        return $this->setData(self::PACK_SEPARATELY, $packSeparately);
    }

    /**
     * {@inheritdoc}
     */
    public function getUnit()
    {
        return $this->getData(self::UNIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUnit($unit)
    {
        return $this->setData(self::UNIT, $unit);
    }

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions');
    }
}
