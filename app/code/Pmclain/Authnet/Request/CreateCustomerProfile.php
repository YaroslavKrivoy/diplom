<?php

namespace Pmclain\Authnet\Request;

use Pmclain\Authnet\CustomerProfile;
use Pmclain\Authnet\PaymentProfile\Address;
use Pmclain\Authnet\ValidationMode;

class CreateCustomerProfile extends AbstractRequest
{
    const REQUEST_NAME = 'createCustomerProfileRequest';
    const FIELD_PROFILE = 'profile';
    const FIELD_VALIDATION_MODE = 'validationMode';

    /**
     * @var CustomerProfile
     */
    private $profile;

    /**
     * @var ValidationMode
     */
    private $validationMode;

    /**
     * @param string $refId
     * @return $this
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
        return $this;
    }

    /**
     * @param CustomerProfile $profile
     * @return $this
     */
    public function setProfile(CustomerProfile $profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * @param ValidationMode $mode
     * @return $this
     */
    public function setValidationMode(ValidationMode $mode)
    {
        $this->validationMode = $mode;
        return $this;
    }

    /**
     * @return array|null
     */
    public function submit()
    {
        $body = [];
        $body[self::FIELD_MERCHANT_AUTH] = $this->getMerchantAuthentication();
        if (isset($this->refId)) {
            $body[self::FIELD_REF_ID] = $this->refId;
        }
        $body[self::FIELD_PROFILE] = $this->profile->toArray();
        if (isset($this->validationMode) && $this->validationMode->get()) {
            $body[self::FIELD_VALIDATION_MODE] = $this->validationMode->get();
        }

        return $this->postRequest([$this->getRequestName() => $body]);
    }

    /**
     * @return string
     */
    public function getRequestName()
    {
        return self::REQUEST_NAME;
    }
}
