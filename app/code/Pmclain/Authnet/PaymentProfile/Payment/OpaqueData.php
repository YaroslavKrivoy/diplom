<?php

namespace Pmclain\Authnet\PaymentProfile\Payment;

use Pmclain\Authnet\Exception\OpaqueDataInformationException;

class OpaqueData implements PaymentInterface
{
    const KEY = 'opaqueData';
    const FIELD_DATA_DESCRIPTOR = 'dataDescriptor';
    const FIELD_DATA_VALUE = 'dataValue';

    /**
     * @var string
     */
    private $dataDescriptor;

    /**
     * @var string
     */
    private $dataValue;

    /**
     * @param string $dataDescriptor
     * @return $this
     */
    public function setDataDescriptor($dataDescriptor)
    {
        $this->dataDescriptor = $dataDescriptor;
        return $this;
    }

    /**
     * @param string $dataValue
     * @return $this
     */
    public function setDataValue($dataValue)
    {
        $this->dataValue = $dataValue;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            self::FIELD_DATA_DESCRIPTOR => $this->getDataDescriptor(),
            self::FIELD_DATA_VALUE => $this->getDataValue(),
        ];
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return self::KEY;
    }

    /**
     * @return string
     * @throws OpaqueDataInformationException
     */
    private function getDataDescriptor()
    {
        if (is_null($this->dataDescriptor)) {
            throw new OpaqueDataInformationException('Opaque Data requires Data Descriptor.');
        }

        return $this->dataDescriptor;
    }

    /**
     * @return string
     * @throws OpaqueDataInformationException
     */
    private function getDataValue()
    {
        if (is_null($this->dataValue)) {
            throw new OpaqueDataInformationException('Opaque Data requires Data Value.');
        }

        return $this->dataValue;
    }
}
