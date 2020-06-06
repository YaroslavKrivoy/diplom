<?php

namespace Pmclain\Authnet\PaymentProfile\Payment;

use Pmclain\Authnet\Exception\CardInformationException;

class CreditCard implements PaymentInterface
{
    const KEY = 'creditCard';
    const FIELD_CARD_NUMBER = 'cardNumber';
    const FIELD_EXPIRATION_DATE = 'expirationDate';
    const FIELD_CARD_CODE = 'cardCode';

    /**
     * @var int
     */
    private $cardNumber;

    /**
     * Format: YYYY-MM
     * @var string
     */
    private $expirationDate;

    /**
     * @var int
     */
    private $cardCode;

    /**
     * @param int $cardNumber
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @param string $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * @param int $cardCode
     * @return $this
     */
    public function setCardCode($cardCode)
    {
        $this->cardCode = $cardCode;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        $cardInfo = [
            self::FIELD_CARD_NUMBER => $this->getCardNumber(),
            self::FIELD_EXPIRATION_DATE => $this->getExpirationDate(),
        ];

        if (!is_null($this->cardCode)) {
            $cardInfo[self::FIELD_CARD_CODE] = $this->cardCode;
        }

        return $cardInfo;
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return self::KEY;
    }

    /**
     * @return int
     * @throws CardInformationException
     */
    private function getCardNumber()
    {
        if (is_null($this->cardNumber)) {
            throw new CardInformationException('Credit Card Number is required.');
        }

        return $this->cardNumber;
    }

    /**
     * @return string
     * @throws CardInformationException
     */
    private function getExpirationDate()
    {
        if (is_null($this->expirationDate)) {
            throw new CardInformationException('Credit Card Expiration Date is required.');
        }

        return $this->expirationDate;
    }
}
