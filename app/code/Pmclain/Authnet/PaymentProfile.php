<?php

namespace Pmclain\Authnet;

use Pmclain\Authnet\PaymentProfile\Address;
use Pmclain\Authnet\PaymentProfile\CustomerType;
use Pmclain\Authnet\PaymentProfile\Payment\PaymentInterface;

class PaymentProfile
{
    const FIELD_CUSTOMER_TYPE = 'customerType';
    const FIELD_BILL_TO = 'billTo';
    const FIELD_PAYMENT = 'payment';
    const FIELD_DEFAULT_PAYMENT_PROFILE = 'defaultPaymentProfile';

    /**
     * @var string
     */
    private $customerType;

    /**
     * @var Address
     */
    private $billTo;

    /**
     * @var PaymentInterface
     */
    private $payment;

    /**
     * @var bool
     */
    private $defaultPaymentProfile = false;

    /**
     * @param string $type
     * @return $this
     */
    public function setCustomerType($type)
    {
        CustomerType::validate($type);
        $this->customerType = $type;
        return $this;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function setBillTo(Address $address)
    {
        $this->billTo = $address;
        return $this;
    }

    /**
     * @param PaymentInterface $payment
     * @return $this
     */
    public function setPayment(PaymentInterface $payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * @param bool $makeDefault
     * @return $this
     */
    public function setDefaultPaymentProfile(bool $makeDefault)
    {
        $this->defaultPaymentProfile = $makeDefault;
        return $this;
    }

    public function toArray()
    {
        $result = [];
        $result[self::FIELD_CUSTOMER_TYPE] = $this->getCustomerType();
        if (isset($this->billTo)) {
            $result[self::FIELD_BILL_TO] = $this->billTo->toArray();
        }
        if (isset($this->payment)) {
            $result[self::FIELD_PAYMENT][$this->payment->getKey()] = $this->payment->toArray();
        }
        $result[self::FIELD_DEFAULT_PAYMENT_PROFILE] = $this->defaultPaymentProfile;

        return $result;
    }

    private function getCustomerType()
    {
        $type = CustomerType::INDIVIDUAL;

        if (isset($this->customerType)) {
            $type = $this->customerType;
        }

        return $type;
    }
}
