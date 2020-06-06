<?php
/**
 * Pmclain_AuthorizenetCim extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category  Pmclain
 * @package   Pmclain_AuthorizenetCim
 * @copyright Copyright (c) 2017-2018
 * @license   Open Software License (OSL 3.0)
 */

namespace Pmclain\AuthorizenetCim\Model\Adapter;

use Pmclain\AuthorizenetCim\Gateway\Config\Config;
use Magento\Framework\Exception\PaymentException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Pmclain\Authnet\TransactionRequest;
use Pmclain\Authnet\CustomerProfile;
use Pmclain\Authnet\MerchantAuthentication;
use Pmclain\AuthorizenetCim\Gateway\Request\AddressDataBuilder;
use Pmclain\AuthorizenetCim\Gateway\Request\CustomerDataBuilder;
use Pmclain\AuthorizenetCim\Gateway\Request\PaymentDataBuilder;
use Pmclain\Authnet\Request\CreateCustomerProfileFactory;
use Pmclain\Authnet\Request\CreateCustomerProfile;
use Pmclain\Authnet\ValidationModeFactory;
use Pmclain\Authnet\ValidationMode;
use Magento\Framework\DataObjectFactory;
use Pmclain\AuthorizenetCim\Model\Authorizenet\Payment;
use Pmclain\Authnet\Request\CreateTransactionFactory;
use Pmclain\Authnet\Request\CreateTransaction;
use Pmclain\Authnet\Request\CreateCustomerPaymentProfileFactory;
use Pmclain\Authnet\Request\CreateCustomerPaymentProfile;

class AuthorizenetAdapter
{
    const ERROR_CODE_DUPLICATE = 'E00039';

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var MerchantAuthentication
     */
    protected $merchantAuth;

    /**
     * @var CreateCustomerProfileFactory
     */
    protected $createCustomerProfileFactory;

    /**
     * @var ValidationModeFactory
     */
    protected $validationModeFactory;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var Payment
     */
    protected $paymentProfile;

    /**
     * @var CreateTransactionFactory
     */
    protected $createTransactionFactory;

    /**
     * @var CreateCustomerPaymentProfileFactory
     */
    protected $createPaymentProfileFactory;
    protected $customer;
    protected $request;
    protected $getCustomerProfileFactory;
    protected $directory;

    /**
     * AuthorizenetAdapter constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param Config $config
     * @param MerchantAuthentication $merchantAuthentication
     * @param CreateCustomerProfileFactory $createCustomerProfileFactory
     * @param ValidationModeFactory $validationModeFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param Payment $paymentProfile
     * @param CreateTransactionFactory $createTransactionFactory
     * @param CreateCustomerPaymentProfileFactory $createPaymentProfileFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Config $config,
        MerchantAuthentication $merchantAuthentication,
        CreateCustomerProfileFactory $createCustomerProfileFactory,
        ValidationModeFactory $validationModeFactory,
        DataObjectFactory $dataObjectFactory,
        Payment $paymentProfile,
        CreateTransactionFactory $createTransactionFactory,
        CreateCustomerPaymentProfileFactory $createPaymentProfileFactory,
        \Pmclain\Authnet\Request\GetCustomerProfileFactory $getProfileFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Filesystem\DirectoryList $directory
    ) {
        $this->customerRepository = $customerRepository;
        $this->config = $config;
        $this->merchantAuth = $merchantAuthentication;
        $this->createCustomerProfileFactory = $createCustomerProfileFactory;
        $this->validationModeFactory = $validationModeFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->paymentProfile = $paymentProfile;
        $this->createTransactionFactory = $createTransactionFactory;
        $this->createPaymentProfileFactory = $createPaymentProfileFactory;
        $this->getCustomerProfileFactory = $getProfileFactory;
        $this->customer = $customer;
        $this->request = $request;
        $this->directory = $directory;
        $this->initMerchantAuthentication();
    }

    public function refund($transaction){
        return $this->submitTransactionRequest($transaction);
    }

    public function void($transaction) {
        return $this->submitTransactionRequest($transaction);
    }

    public function submitForSettlement($transaction){
        return $this->submitTransactionRequest($transaction);
    }

    public function saleForNewProfile(array $data)
    {   
        $data[PaymentDataBuilder::PAYMENT]->setBillTo($data[AddressDataBuilder::BILL_TO]);
        $data[CustomerDataBuilder::CUSTOMER_PROFILE]->setPaymentProfile($data[PaymentDataBuilder::PAYMENT]);
        $customerProfileResponse = $this->createCustomerProfile($data[CustomerDataBuilder::CUSTOMER_PROFILE]);
        if($customerProfileResponse->getData('profile')){
            $data[CustomerDataBuilder::PROFILE_ID] = $customerProfileResponse->getData('profile')->getData('customerProfileId');
        } else {
            $data[CustomerDataBuilder::PROFILE_ID] = $customerProfileResponse->getData('customerProfileId');
        }
        $result = $this->createCustomerPaymentProfile($customerProfileResponse, $data);
        $data[PaymentDataBuilder::PAYMENT_PROFILE] = $result->getData('customerPaymentProfileId');
        $this->paymentProfile->setProfileId($data[PaymentDataBuilder::PAYMENT_PROFILE]);
        if ($data[CustomerDataBuilder::CUSTOMER_ID]) {
            $this->saveCustomerProfileId($data[CustomerDataBuilder::CUSTOMER_ID], $data[CustomerDataBuilder::PROFILE_ID]);
        }
        return $this->sale($data);
    }

    public function saleForExistingProfile(array $data) {
        return $this->saleForNewProfile($data);
        $data[PaymentDataBuilder::PAYMENT]->setBillTo($data[AddressDataBuilder::BILL_TO]);
        $customerPaymentProfileResponse = $this->createCustomerPaymentProfile($data);
        $data[PaymentDataBuilder::PAYMENT_PROFILE] = $customerPaymentProfileResponse->getData('customerPaymentProfileId');
        $this->paymentProfile->setProfileId($data[PaymentDataBuilder::PAYMENT_PROFILE]);
        return $this->sale($data);
    }

    public function saleForVault(array $data) {
        $this->fixCard($data['payment_profile'], $data['profile_id'], $data);
        $customerProfileResponse = $this->createCustomerProfile($data[CustomerDataBuilder::CUSTOMER_PROFILE]);
        if($customerProfileResponse->getData('profile')){
            $data[CustomerDataBuilder::PROFILE_ID] = $customerProfileResponse->getData('profile')->getData('customerProfileId');
        } else {
            $data[CustomerDataBuilder::PROFILE_ID] = $customerProfileResponse->getData('customerProfileId');
        }
        $data[PaymentDataBuilder::PAYMENT]->setBillTo($data[AddressDataBuilder::BILL_TO]);
        return $this->sale($data);
    }

    protected function sale(array $data) {
        if(!isset($data[PaymentDataBuilder::TRANSACTION_REQUEST])){
            $data[PaymentDataBuilder::TRANSACTION_REQUEST]=new \Magento\Framework\DataObject();
        }
        $data[PaymentDataBuilder::TRANSACTION_REQUEST]->setCustomerProfileId($data[CustomerDataBuilder::PROFILE_ID]);
        $data[PaymentDataBuilder::TRANSACTION_REQUEST]->setPaymentProfileId($data[PaymentDataBuilder::PAYMENT_PROFILE]);
        if ($data[PaymentDataBuilder::CAPTURE]) {
            $data[PaymentDataBuilder::TRANSACTION_REQUEST]->setTransactionType(TransactionRequest\TransactionType::TYPE_AUTH_CAPTURE);
        }

        return $this->submitTransactionRequest($data[PaymentDataBuilder::TRANSACTION_REQUEST]);
    }

    /**
     * @param $customerId
     * @param $customerProfileId
     * @return $this
     */
    protected function saveCustomerProfileId($customerId, $customerProfileId) {
        $customer = $this->customerRepository->getById($customerId);
        $customer->setCustomAttribute('authorizenet_cim_profile_id', $customerProfileId);
        $this->customerRepository->save($customer);
        return $this;
    }

    protected function submitTransactionRequest($transaction) {
        $createTransaction = $this->createTransactionFactory->create(['sandbox' => $this->getIsSandbox()]);
        $createTransaction->setMerchantAuthentication($this->merchantAuth);
        $createTransaction->setTransactionRequest($transaction);
        $data = $this->createDataObject($createTransaction->submit());
        return $data;
    }

    protected function getMerchantCustomerId($profile){
        try {
            $customer = $this->customer->setWebsiteId(1)->loadByEmail($profile->getEmail());
            if($customer->getId()){
                return $this->config->getIdPrefix().$customer->getId();
            }
        } catch(\Exception $e){}
        return $this->config->getIdPrefix().bin2hex(openssl_random_pseudo_bytes(3));
    }

    protected function checkForDuplicateCustomer($profile){
        $request = $this->getCustomerProfileFactory->create(['sandbox' => $this->getIsSandbox()]);
        $request->setMerchantCustomerId($profile->getCustomerId());
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setValidationMode($this->getValidationMode());
        $result = $this->createDataObject($request->submit());
        if($result->getMessages()->getData('resultCode') == 'Error'){
            return null;
        }
        return $result;
    }

    protected function checkForDuplicatePayment($customer, $data){
        if($customer->getData('customerPaymentProfileIdList')){
            return $customer->getData('customerPaymentProfileIdList')[0];
        }
        if($customer && $customer->getData('profile') && $customer->getData('profile')->getData('paymentProfiles')){
            foreach($customer->getData('profile')->getData('paymentProfiles') as $profile){
                $info = $profile->getData('payment')->getData('creditCard');
                if($info->getData('cardNumber') == 'XXXX'.$data['payment_info']['cc_last4']){
                    return $profile;
                }
            }
        }
        return null;
    }

    /**
     * @param CustomerProfile $customerProfile
     * @return \Magento\Framework\DataObject
     * @throws PaymentException
     */
    protected function createCustomerProfile($customerProfile) {
        $customerProfile->setCustomerId($this->getMerchantCustomerId($customerProfile));
        $test = $this->checkForDuplicateCustomer($customerProfile);
        if($test){
            return $test;
        }
        $customerProfileRequest = $this->createCustomerProfileFactory->create(['sandbox' => $this->getIsSandbox()]);
        $customerProfileRequest->setProfile($customerProfile);
        $customerProfileRequest->setMerchantAuthentication($this->merchantAuth);
        $customerProfileRequest->setValidationMode($this->getValidationMode());
        $customerProfileResponse = $customerProfileRequest->submit();
        $log = fopen($this->directory->getRoot().'/var/log/authnet.log', 'a+');
        ob_start();
        print_r($customerProfileResponse);
        fwrite($log, ob_get_clean());
        fclose($log);
        $result = $this->createDataObject($customerProfileResponse);

        if ($result->getMessages()->getData('resultCode') === 'Error') {
            if ($result->getMessages()->getMessage()[0]->getCode() !== self::ERROR_CODE_DUPLICATE) {
                throw new PaymentException(
                    __($result->getMessages()->getMessage()[0]->getText())
                );
            }
        }
        return $result;
    }

    protected function createCustomerPaymentProfile($customer, array $data) {
        $test = $this->checkForDuplicatePayment($customer, $data);
        if($test){
            $return = $test;
            if(!is_object($test)){
                $return = new \Magento\Framework\DataObject(['customerPaymentProfileId' => $test]);
            }
            $this->fixCard($return->getData('customerPaymentProfileId'), $customer->getProfile()['customerProfileId'], $data);
            return $return;
        }
        $createPaymentProfileRequest = $this->createPaymentProfileFactory->create(['sandbox' => $this->getIsSandbox()]);
        $createPaymentProfileRequest->setMerchantAuthentication($this->merchantAuth);
        $createPaymentProfileRequest->setCustomerProfileId($data[CustomerDataBuilder::PROFILE_ID]);
        $createPaymentProfileRequest->setPaymentProfile($data[PaymentDataBuilder::PAYMENT]);
        $createPaymentProfileRequest->setValidationMode($this->getValidationMode());
        $response = $createPaymentProfileRequest->submit();
        $log = fopen($this->directory->getRoot().'/var/log/authnet.log', 'a+');
        ob_start();
        print_r($response);
        fwrite($log, ob_get_clean());
        fclose($log);
        
        $result = $this->createDataObject($response);
        if ($result->getMessages()->getData('resultCode') === 'Error') {
            if ($result->getMessages()->getMessage()[0]->getCode() !== self::ERROR_CODE_DUPLICATE) {
                throw new PaymentException(
                    __($result->getMessages()->getMessage()[0]->getText())
                );
            }
        }

        return $result;
    }

    /**
     * @return $this
     */
    protected function initMerchantAuthentication()
    {
        $this->merchantAuth->setLoginId($this->config->getApiLoginId());
        $this->merchantAuth->setTransactionKey($this->config->getTransactionKey());

        return $this;
    }

    /**
     * @return bool
     */
    protected function getIsSandbox()
    {
        return $this->config->isTest();
    }

    /**
     * @return ValidationMode
     */
    protected function getValidationMode()
    {
        /**
         * @var ValidationMode $validationMode
         */
        $validationMode = $this->validationModeFactory->create();

        try {
            $validationMode->set($this->config->getValidationMode());
            return $validationMode;
        } catch (\Pmclain\Authnet\Exception\InputException $e) {
            return $validationMode;
        }
    }

    protected function fixCard($payment, $customer, $data){
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
                            'cardNumber' => ((isset($profile['paymentProfile']['payment']['creditCard']['cardNumber']))?$profile['paymentProfile']['payment']['creditCard']['cardNumber']:''),
                            'expirationDate' => ((isset($profile['paymentProfile']['payment']['creditCard']['expirationDate']))?$profile['paymentProfile']['payment']['creditCard']['expirationDate']:'')
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

    /**
     * @param array $data
     * @return array|\Magento\Framework\DataObject
     */
    protected function createDataObject($data)
    {
        $convert = false;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->createDataObject($value);
            }
            if (!is_numeric($key)) {
                $convert = true;
            }
        }
        return $convert ? $this->dataObjectFactory->create(['data' => $data]) : $data;
    }

    private function removeUtf8Bom($string)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $string);

        return $text;
    }

}