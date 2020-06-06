<?php

namespace Pmclain\Authnet\TransactionRequest;

use Pmclain\Authnet\Exception\InputException;

class TransactionType
{
    const TYPE_REFUND = 'refundTransaction';
    const TYPE_AUTH_CAPTURE = 'authCaptureTransaction';
    const TYPE_AUTH_ONLY = 'authOnlyTransaction';
    const TYPE_GET_DETAILS = 'getDetailsTransaction';
    const TYPE_AUTH_ONLY_CONTINUE = 'authOnlyContinueTransaction';
    const TYPE_PRIOR_AUTH_CAPTURE = 'priorAuthCaptureTransaction';
    const TYPE_AUTH_CAPTURE_CONTINUE = 'authCaptureContinueTransaction';
    const TYPE_VOID = 'voidTransaction';
    const TYPE_CAPTURE_ONLY = 'captureOnlyTransaction';

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
            self::TYPE_REFUND,
            self::TYPE_AUTH_CAPTURE,
            self::TYPE_AUTH_ONLY,
            self::TYPE_GET_DETAILS,
            self::TYPE_AUTH_ONLY_CONTINUE,
            self::TYPE_PRIOR_AUTH_CAPTURE,
            self::TYPE_AUTH_CAPTURE_CONTINUE,
            self::TYPE_VOID,
            self::TYPE_CAPTURE_ONLY,
        ];
    }
}
