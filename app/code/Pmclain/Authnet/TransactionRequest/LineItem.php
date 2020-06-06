<?php

namespace Pmclain\Authnet\TransactionRequest;

class LineItem
{
    const FIELD_ITEM_ID = 'itemId';
    const FIELD_NAME = 'name';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_UNIT_PRICE = 'unitPrice';
    const FIELD_TAXABLE = 'taxable';

    /**
     * @var string
     */
    private $itemId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int|float
     */
    private $quantity;

    /**
     * @var int|float
     */
    private $unitPrice;

    /**
     * @var bool
     */
    private $taxable;

    /**
     * @param string $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param int|float $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param int|float $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @param bool $taxable
     * @return $this
     */
    public function setTaxable(bool $taxable)
    {
        $this->taxable = $taxable;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        if (isset($this->itemId)) {
            $result[self::FIELD_ITEM_ID] = $this->itemId;
        }
        if (isset($this->name)) {
            $result[self::FIELD_NAME] = $this->name;
        }
        if (isset($this->description)) {
            $result[self::FIELD_DESCRIPTION] = $this->description;
        }
        if (isset($this->quantity)) {
            $result[self::FIELD_QUANTITY] = $this->quantity;
        }
        if (isset($this->unitPrice)) {
            $result[self::FIELD_UNIT_PRICE] = $this->unitPrice;
        }
        if (isset($this->taxable)) {
            $result[self::FIELD_TAXABLE] = $this->taxable;
        }

        return $result;
    }
}
