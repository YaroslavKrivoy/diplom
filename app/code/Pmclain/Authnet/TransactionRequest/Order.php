<?php

namespace Pmclain\Authnet\TransactionRequest;

class Order
{
    const FIELD_INVOICE_NUMBER = 'invoiceNumber';
    const FIELD_DESCRIPTION = 'description';

    /**
     * @var string
     */
    private $invoiceNumber;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $invoiceNumber
     * @return $this
     */
    public function setInvoiceNumber(string $invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
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
        if (isset($this->invoiceNumber)) {
            $result[self::FIELD_INVOICE_NUMBER] = $this->invoiceNumber;
        }
        if (isset($this->description)) {
            $result[self::FIELD_DESCRIPTION] = $this->description;
        }

        return $result;
    }
}
