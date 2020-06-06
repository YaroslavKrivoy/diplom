<?php

namespace Pmclain\Authnet\Request;

use Pmclain\Authnet\Exception\MissingDataException;
use Pmclain\Authnet\PaymentProfile;
use Pmclain\Authnet\ValidationMode;

class CreateCustomerPaymentProfile extends AbstractRequest
{
    const REQUEST_NAME = 'createCustomerPaymentProfileRequest';
    const FIELD_CUSTOMER_PROFILE_ID = 'customerProfileId';
    const FIELD_PAYMENT_PROFILE = 'paymentProfile';
    const FIELD_VALIDATION_MODE = 'validationMode';

    /**
     * @var string|int
     */
    private $customerProfileId;

    /**
     * @var PaymentProfile
     */
    private $paymentProfile;

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
     * @param string|int $customerProfileId
     * @return $this
     */
    public function setCustomerProfileId($customerProfileId)
    {
        $this->customerProfileId = $customerProfileId;
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
        $body[self::FIELD_CUSTOMER_PROFILE_ID] = $this->customerProfileId;
        $body[self::FIELD_PAYMENT_PROFILE] = $this->getPaymentProfile();
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

    private function getPaymentProfile()
    {
        if (isset($this->paymentProfile)) {
            return $this->paymentProfile->toArray();
        }

        throw new MissingDataException('Create payment profile request requires payment profile.');
    }
}
