<?php

namespace Pmclain\Authnet;

use Pmclain\Authnet\PaymentProfile\Address;
use Pmclain\Authnet\PaymentProfile\Payment\PaymentInterface;

class CustomerProfile
{
    const FIELD_CUSTOMER_PROFILE_ID = 'customerProfileId';
    const FIELD_CUSTOMER_ID = 'merchantCustomerId';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_EMAIL = 'email';
    const FIELD_PAYMENT_PROFILES = 'paymentProfiles';
    const FIELD_SHIP_TO_LIST = 'shipToList';

    /**
     * @var string
     */
    private $merchantCustomerId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $email;

    /**
     * @var PaymentProfile
     */
    private $paymentProfile;

    /**
     * @var Address[]
     */
    private $shipToList;

    /**
     * @param string $merchantCustomerId
     * @return $this
     */
    public function setCustomerId($merchantCustomerId)
    {
        $this->merchantCustomerId = $merchantCustomerId;
        return $this;
    }

    public function getCustomerId(){
        return $this->merchantCustomerId;
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

    public function setProfileId($id){
        $this->profileId = $id;
        return $this;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param PaymentProfile $paymentProfile
     * @return $this
     */
    public function setPaymentProfile(PaymentProfile $paymentProfile)
    {
        $this->paymentProfile = $paymentProfile;
        return $this;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function addShipTo(Address $address)
    {
        $this->shipToList[] = $address->toArray();
        return $this;
    }

    public function getEmail(){
        return $this->email;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        if (isset($this->merchantCustomerId)) {
            $result[self::FIELD_CUSTOMER_ID] = $this->merchantCustomerId;
        }
        if (isset($this->description)) {
            $result[self::FIELD_DESCRIPTION] = $this->description;
        }
        if (isset($this->email)) {
            $result[self::FIELD_EMAIL] = $this->email;
        }
        if (isset($this->paymentProfile)) {
            $result[self::FIELD_PAYMENT_PROFILES] = $this->paymentProfile->toArray();
        }
        if (count($this->shipToList)) {
            $result[self::FIELD_SHIP_TO_LIST] = $this->shipToList;
        }
        if(isset($this->profileId)){
            $result[self::FIELD_CUSTOMER_PROFILE_ID] = $this->profileId;
        }

        return $result;
    }
}
