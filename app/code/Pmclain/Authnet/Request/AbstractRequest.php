<?php

namespace Pmclain\Authnet\Request;

use Pmclain\Authnet\Exception\MissingAuthException;
use Pmclain\Authnet\MerchantAuthentication;

abstract class AbstractRequest
{
    const FIELD_MERCHANT_AUTH = 'merchantAuthentication';
    const FIELD_REF_ID = 'refId';

    const URL_SANDBOX = 'https://apitest.authorize.net/xml/v1/request.api';
    const URL_PRODUCTION = 'https://api.authorize.net/xml/v1/request.api';

    /**
     * @var string
     */
    protected $url;

    /**
     * @var MerchantAuthentication
     */
    protected $merchantAuthentication;

    /**
     * @var string
     */
    protected $refId;

    /**
     * AbstractRequest constructor.
     * @param bool $sandbox
     */
    public function __construct($sandbox = false)
    {
        if ($sandbox) {
            $this->url = self::URL_SANDBOX;
        } else {
            $this->url = self::URL_PRODUCTION;
        }
    }

    abstract public function submit();

    /**
     * @return string
     */
    abstract public function getRequestName();

    /**
     * @param MerchantAuthentication $merchantAuthentication
     * @return $this
     */
    public function setMerchantAuthentication(MerchantAuthentication $merchantAuthentication)
    {
        $this->merchantAuthentication = $merchantAuthentication;
        return $this;
    }

    /**
     * @param array $request
     * @return array|null
     */
    protected function postRequest(array $request)
    {
        $content = json_encode($request);

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json;',
            'Accept: application/json',
            'Content-Length: ' . strlen($content),
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($this->removeUtf8Bom($result), true);
    }

    /**
     * @return array
     * @throws MissingAuthException
     */
    protected function getMerchantAuthentication()
    {
        if (is_null($this->merchantAuthentication)) {
            throw new MissingAuthException('Request Require Merchant Authentication.');
        }

        return $this->merchantAuthentication->toArray();
    }

    /**
     * @param $string
     * @return string
     */
    private function removeUtf8Bom($string)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $string);

        return $text;
    }
}
