<?php

namespace Pmclain\Authnet\TransactionRequest;

class TaxDutyShipping
{
    const FIELD_AMOUNT = 'amount';
    const FIELD_NAME = 'name';
    const FIELD_DESCRIPTION = 'description';

    /**
     * @var int|float
     */
    private $amount;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @param int|float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
     * @return array
     */
    public function toArray()
    {
        $result = [];
        if (isset($this->amount)) {
            $result[self::FIELD_AMOUNT] = $this->amount;
        }
        if (isset($this->name)) {
            $result[self::FIELD_NAME] = $this->name;
        }
        if (isset($this->description)) {
            $result[self::FIELD_DESCRIPTION] = $this->description;
        }

        return $result;
    }
}
