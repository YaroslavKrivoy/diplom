<?php

namespace Pmclain\Authnet\Request;

use Pmclain\Authnet\TransactionRequest;
use Pmclain\Authnet\Exception\MissingDataException;

class CreateTransaction extends AbstractRequest
{
    const REQUEST_NAME = 'createTransactionRequest';
    const FIELD_TRANSACTION_REQUEST = 'transactionRequest';

    /**
     * @var TransactionRequest
     */
    private $transactionRequest;

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
        $body[self::FIELD_TRANSACTION_REQUEST] = $this->getTransactionRequest();

        return $this->postRequest([$this->getRequestName() => $body]);
    }

    /**
     * @param TransactionRequest $request
     * @return $this
     */
    public function setTransactionRequest(TransactionRequest $request)
    {
        $this->transactionRequest = $request;
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
     * @return mixed
     * @throws MissingDataException
     */
    private function getTransactionRequest()
    {
        if (isset($this->transactionRequest)) {
            return $this->transactionRequest->toArray();
        }

        throw new MissingDataException(
            sprintf('Create transaction requires instance of %s', TransactionRequest::class)
        );
    }
}
