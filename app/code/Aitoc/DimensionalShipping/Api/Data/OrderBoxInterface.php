<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api\Data;

interface OrderBoxInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'id';
    const ORDER_ID = 'order_id';
    const BOX_ID = 'box_id';
    const WEIGHT = 'weight';

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
     * Returns box_id field
     *
     * @return int|null
     */
    public function getBoxId();

    /**
     * @param int $boxId
     *
     * @return $this
     */
    public function setBoxId($boxId);

    /**
     * Returns order_id field
     *
     * @return $this
     */
    public function getOrderId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Returns weight field
     *
     * @return $this
     */
    public function getWeight();

    /**
     * @param $weight
     *
     * @return mixed
     */
    public function setWeight($weight);
}
