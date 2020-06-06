<?php

namespace Pmclain\Authnet;

use Pmclain\Authnet\Exception\MissingAuthException;

class MerchantAuthentication
{
    const FIELD_NAME = 'name';
    const FIELD_TRANSACTION_KEY = 'transactionKey';

    /**
     * @var string
     */
    private $loginId;

    /**
     * @var string
     */
    private $transactionKey;

    /**
     * MerchantAuthentication constructor.
     * @param null|string $loginId
     * @param null|string $transactionKey
     */
    public function __construct(
        $loginId = null,
        $transactionKey = null
    ) {
        $this->setLoginId($loginId);
        $this->setTransactionKey($transactionKey);
    }

    /**
     * @param string $loginId
     * @return self
     */
    public function setLoginId($loginId)
    {
        $this->loginId = $loginId;
        return $this;
    }

    /**
     * @param string $transactionKey
     * @return $this
     */
    public function setTransactionKey($transactionKey)
    {
        $this->transactionKey = $transactionKey;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::FIELD_NAME => $this->getLoginId(),
            self::FIELD_TRANSACTION_KEY => $this->getTransactionKey(),
        ];
    }

    /**
     * @return string
     * @throws MissingAuthException
     */
    private function getLoginId()
    {
        if (is_null($this->loginId)) {
            throw new MissingAuthException('Merchant Login ID was not provided.');
        }

        return $this->loginId;
    }

    /**
     * @return string
     * @throws MissingAuthException
     */
    private function getTransactionKey()
    {
        if (is_null($this->transactionKey)) {
            throw new MissingAuthException('Merchant Transaction Key was not provided.');
        }

        return $this->transactionKey;
    }
}
