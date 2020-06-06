<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 03.10.19
 * Time: 17:10
 */

namespace KozakGroup\RewriteOrderEditor\Model\Adapter;

use Magento\Framework\DataObjectFactory;
use Pmclain\Authnet\Request\CreateCustomerPaymentProfileFactory;
use Pmclain\Authnet\Request\CreateCustomerProfileFactory;
use Pmclain\Authnet\Request\CreateTransactionFactory;
use Pmclain\Authnet\ValidationModeFactory;
use Pmclain\AuthorizenetCim\Gateway\Request\AddressDataBuilder;

class AuthorizenetAdapter extends \Pmclain\AuthorizenetCim\Model\Adapter\AuthorizenetAdapter
{



    protected function fixCard($payment, $customer, $data)
    {
        $url = ($this->getIsSandbox())?'https://apitest.authorize.net/xml/v1/request.api':'https://api.authorize.net/xml/v1/request.api';
        $content = json_encode([
            'getCustomerPaymentProfileRequest' => [
                'merchantAuthentication' => $this->merchantAuth->toArray(),
                'customerProfileId' => $customer,
                'customerPaymentProfileId' => $payment,
                'includeIssuerInfo' => 'true'
            ]
        ]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($content),
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($this->removeUtf8Bom($result), true);
        foreach($data[AddressDataBuilder::BILL_TO]->toArray() as $key => $val){
            $profile['paymentProfile']['billTo'][$key] = $val;
        }
        if($profile['paymentProfile']['billTo']['country'] == 'US'){
            $profile['paymentProfile']['billTo']['country'] = 'USA';
        }
        $cc_number = $this->request->getParam('pmclain_authorizenetcim_cc_number');
        $cc_month = $this->request->getParam('pmclain_authorizenetcim_expiration');
        if(strlen($cc_month) == 1)
        {
            $cc_month = "0".$cc_month;
        }
        $cc_date = $cc_month . '/' . $this->request->getParam('pmclain_authorizenetcim_expiration_yr');
        if(empty($cc_number)){
            $cc_number = ((isset($profile['paymentProfile']['payment']['creditCard']['cardNumber']))?$profile['paymentProfile']['payment']['creditCard']['cardNumber']:'');
            $cc_date = ((isset($profile['paymentProfile']['payment']['creditCard']['expirationDate']))?$profile['paymentProfile']['payment']['creditCard']['expirationDate']:'');
        }
        $content = json_encode([
            'updateCustomerPaymentProfileRequest' => [
                'merchantAuthentication' => $this->merchantAuth->toArray(),
                'customerProfileId' => $customer,
                'paymentProfile' => [
                    'billTo' => [
                        'firstName' => ((isset($profile['paymentProfile']['billTo']['firstName']))?$profile['paymentProfile']['billTo']['firstName']:''),
                        'lastName' => ((isset($profile['paymentProfile']['billTo']['lastName']))?$profile['paymentProfile']['billTo']['lastName']:''),
                        'company' => ((isset($profile['paymentProfile']['billTo']['company']))?$profile['paymentProfile']['billTo']['company']:''),
                        'address' => ((isset($profile['paymentProfile']['billTo']['address']))?$profile['paymentProfile']['billTo']['address']:''),
                        'city' => ((isset($profile['paymentProfile']['billTo']['city']))?$profile['paymentProfile']['billTo']['city']:''),
                        'state' => ((isset($profile['paymentProfile']['billTo']['state']))?$profile['paymentProfile']['billTo']['state']:''),
                        'zip' => ((isset($profile['paymentProfile']['billTo']['zip']))?$profile['paymentProfile']['billTo']['zip']:''),
                        'country' => ((isset($profile['paymentProfile']['billTo']['country']))?$profile['paymentProfile']['billTo']['country']:''),
                        'phoneNumber' => ((isset($profile['paymentProfile']['billTo']['phoneNumber']))?$profile['paymentProfile']['billTo']['phoneNumber']:''),
                        'faxNumber' => ((isset($profile['paymentProfile']['billTo']['faxNumber']))?$profile['paymentProfile']['billTo']['faxNumber']:'')
                    ],
                    'payment' => [
                        'creditCard' => [
                            'cardNumber' => $cc_number,
                            'expirationDate' => $cc_date
                        ]
                    ],
                    'defaultPaymentProfile' => (isset($profile['paymentProfile']['defaultPaymentProfile']))?$profile['paymentProfile']['defaultPaymentProfile']:'true',
                    'customerPaymentProfileId' => (isset($profile['paymentProfile']['customerPaymentProfileId']))?$profile['paymentProfile']['customerPaymentProfileId']:''
                ],
                'validationMode' => $this->getValidationMode()->get()
            ]
        ]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($content),
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($this->removeUtf8Bom($result), true);
    }

    private function removeUtf8Bom($string)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $string);

        return $text;
    }

}