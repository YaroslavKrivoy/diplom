<?php

namespace Pmclain\Authnet\Request;

class GetCustomerProfileIds extends AbstractRequest
{
    const REQUEST_NAME = 'getCustomerProfileIdsRequest';

    /**
     * @return array|null
     */
    public function getRequestName()
    {
        return self::REQUEST_NAME;
    }

    public function submit()
    {
        return $this->postRequest([
            $this->getRequestName() => [
                self::FIELD_MERCHANT_AUTH => $this->getMerchantAuthentication(),
            ],
        ]);
    }
}
