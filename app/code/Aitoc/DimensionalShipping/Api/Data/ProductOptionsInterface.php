<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api\Data;

interface ProductOptionsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'item_id';
    const PRODUCT_ID = 'product_id';
    const WEIGHT = 'weight';
    const HEIGHT = 'height';
    const WIDTH = 'width';
    const LENGTH = 'length';
    const SELECT_BOX = 'select_box';
    const SPECIAL_BOX = 'special_box';
    const PACK_SEPARATELY = 'pack_separately';
    const UNIT = 'unit';

    /**
     * Returns entity_id field
     *
     * @return int|null
     */
    public function getItemId();

    /**
     * @param int $itemId
     *
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Returns name field
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Returns width field
     *
     * @return int|null
     */
    public function getWidth();

    /**
     * @param float $width
     *
     * @return $this
     */
    public function setWidth($width);

    /**
     * Returns width field
     *
     * @return mixed
     */
    public function getLength();

    /**
     * @param $length
     *
     * @return mixed
     */
    public function setLength($length);

    /**
     * Returns height field
     *
     * @return int|null
     */
    public function getHeight();

    /**
     * @param float $height
     *
     * @return $this
     */
    public function setHeight($height);

    /**
     * Returns special_box field
     *
     * @return int|null
     */
    public function getSpecialBox();

    /**
     * Returns select_box field
     *
     * @return int|null
     */
    public function getSelectBox();

    /**
     * @param int $selectBox
     *
     * @return $this
     */
    public function setSelectBox($selectBox);

    /**
     * @param float $specialBox
     *
     * @return $this
     */
    public function setSpecialBox($specialBox);

    /**
     * Returns pack_separately field
     *
     * @return int|null
     */
    public function getPackSeparately();

    /**
     * @param float $packSeparately
     *
     * @return $this
     */
    public function setPackSeparately($packSeparately);

    /**
     * Returns unit field
     *
     * @return int|null
     */
    public function getUnit();

    /**
     * @param string $unit
     *
     * @return mixed
     */
    public function setUnit($unit);
}
