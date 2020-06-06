<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Model;

class Box extends \Magento\Framework\Model\AbstractModel implements \Aitoc\DimensionalShipping\Api\Data\BoxInterface
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
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
     * {@inheritdoc}
     */
    public function getEmptyWeight()
    {
        return $this->getData(self::EMPTY_WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmptyWeight($weight)
    {
        return $this->setData(self::EMPTY_WEIGHT, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneDayWeight()
    {
        return $this->getData(self::ONE_DAY_WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setOneDayWeight($weight)
    {
        return $this->setData(self::ONE_DAY_WEIGHT, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getTwoDayWeight()
    {
        return $this->getData(self::TWO_DAY_WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setTwoDayWeight($weight)
    {
        return $this->setData(self::TWO_DAY_WEIGHT, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getThreeDayWeight()
    {
        return $this->getData(self::THREE_DAY_WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setThreeDayWeight($weight)
    {
        return $this->setData(self::THREE_DAY_WEIGHT, $weight);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFourDayWeight()
    {
        return $this->getData(self::FOUR_DAY_WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFourDayWeight($weight)
    {
        return $this->setData(self::FOUR_DAY_WEIGHT, $weight);
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
    public function getOuterHeight()
    {
        return $this->getData(self::OUTER_HEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setOuterHeight($height)
    {
        return $this->setData(self::OUTER_HEIGHT, $height);
    }

    /**
     * {@inheritdoc}
     */
    public function getOuterWidth()
    {
        return $this->getData(self::OUTER_WIDTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setOuterWidth($width)
    {
        return $this->setData(self::OUTER_WIDTH, $width);
    }

    /**
     * {@inheritdoc}
     */
    public function getOuterLength()
    {
        return $this->getData(self::OUTER_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setOuterLength($length)
    {
        return $this->setData(self::OUTER_LENGTH, $length);
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
        $this->_init('Aitoc\DimensionalShipping\Model\ResourceModel\Box');
    }
}
