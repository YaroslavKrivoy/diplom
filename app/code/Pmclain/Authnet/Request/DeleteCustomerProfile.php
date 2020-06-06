<?php

namespace Pmclain\Authnet\Request;

class DeleteCustomerProfile extends AbstractRequest
{
    const REQUEST_NAME = 'deleteCustomerProfileRequest';
    const FIELD_CUSTOMER_PROFILE_ID = 'customerProfileId';

    /**
     * @var string|int
     */
    private $customerProfileId;

    /**
     * @param $customerProfileId
     * @return $this
     */
    public function setCustomerProfileId($customerProfileId)
    {
        $this->customerProfileId = $customerProfileId;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestName()
    {
        return self::REQUEST_NAME;
    }

    /**
     * @return array|null
     */
    public function submit()
    {
        return $this->postRequest([
            $this->getRequestName() => [
                self::FIELD_MERCHANT_AUTH => $this->getMerchantAuthentication(),
                self::FIELD_CUSTOMER_PROFILE_ID => $this->customerProfileId,
            ],
        ]);
    }
}
