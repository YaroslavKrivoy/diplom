<?php

namespace Pmclain\Authnet\PaymentProfile\Payment;

interface PaymentInterface
{
    public function getKey() : string;

    public function toArray() : array;
}
