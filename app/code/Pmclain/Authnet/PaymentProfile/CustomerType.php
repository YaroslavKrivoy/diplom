<?php

namespace Pmclain\Authnet\PaymentProfile;

use Pmclain\Authnet\Exception\InputException;

class CustomerType
{
    const INDIVIDUAL = 'individual';
    const BUSINESS = 'business';

    /**
     * @param string $string
     * @return bool
     * @throws InputException
     */
    public static function validate($string)
    {
        if (in_array($string, self::getTypes())) {
            return true;
        }

        throw new InputException('Input was not valid Transaction Type.');
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::BUSINESS,
            self::INDIVIDUAL,
        ];
    }
}
