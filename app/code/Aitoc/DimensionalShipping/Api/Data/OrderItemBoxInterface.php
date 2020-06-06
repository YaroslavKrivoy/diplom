<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\DimensionalShipping\Api\Data;

interface OrderItemBoxInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'item_id';
    const ORDER_ITEM_ID = 'order_item_id';
    const BOX_ID = 'order_box_id';
    const SEPARATE = 'separate';
    const QTY = 'qty';
    const ORDER_ID = 'order_id';
    const SKU = 'sku';
    const ERROR_MESSAGE = 'error_message';
    const NOT_PACKED = 'not_packed';

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
     * Returns order_item_id field
     *
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * @param int $orderItemId
     *
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * Returns box_id field
     *
     * @return int|null
     */
    public function getOrderBoxId();

    /**
     * @param int $boxId
     *
     * @return $this
     */
    public function setOrderBoxId($boxId);

    /**
     * Returns separate field
     *
     * @return mixed
     */
    public function getSeparate();

    /**
     * @param boolean $value
     *
     * @return mixed
     */
    public function setSeparate($value);

    /**
     * Returns qty field
     *
     * @return mixed
     */
    public function getQty();

    /**
     * @param int $value
     *
     * @return mixed
     */
    public function setQty($qty);

    /**
     * Returns order_id field
     *
     * @return mixed
     */
    public function getOrderId();

    /**
     * @param int $value
     *
     * @return mixed
     */
    public function setOrderId($orderId);

    /**
     * Returns sku field
     *
     * @return mixed
     */
    public function getSku();

    /**
     * @param string $sku
     *
     * @return mixed
     */
    public function setSku($sku);

    /**
     * Returns error_message field
     *
     * @return mixed
     */
    public function getErrorMessage();

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function setErrorMessage($message);
    
    /**
     * Returns not_packed field
     *
     * @return mixed
     */
    public function getNotPacked();

    /**
     * @param boolean $notPacked
     *
     * @return mixed
     */
    public function setNotPacked($notPacked);
}
