<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 11.11.19
 * Time: 9:40
 */

namespace KozakGroup\RewriteOrderEditor\Model\Checkout;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteAddressValidator;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\ShippingFactory;
use Psr\Log\LoggerInterface as Logger;

class ShippingInformationManagementPlugin extends \MageArray\StorePickup\Model\Checkout\ShippingInformationManagementPlugin
{

    protected $cartExtensionFactory;

    protected $shippingFactory;

    public function __construct(\Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement, \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory, \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, QuoteAddressValidator $addressValidator, Logger $logger, \Magento\Customer\Api\AddressRepositoryInterface $addressRepository, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector, \MageArray\StorePickup\Helper\Data $dataHelper)
    {
        parent::__construct($paymentMethodManagement, $paymentDetailsFactory, $cartTotalsRepository, $quoteRepository, $addressValidator, $logger, $addressRepository, $scopeConfig, $totalsCollector, $dataHelper);
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function saveAddressInformation(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();
        $carrierCode = $addressInformation->getShippingCarrierCode();
        $methodCode = $addressInformation->getShippingMethodCode();

        if (!$address->getCustomerAddressId()) {
            $address->setCustomerAddressId(null);
        }

        if (!$address->getCountryId()) {
            throw new StateException(__('Shipping address is not set'));
        }
        $pickupDate = '';
        $pickupStore = '1';
        $pickupTime = '';
        $extAttributes = $addressInformation->getExtensionAttributes();
        if(!empty($extAttributes)){

            $pickupDate = $extAttributes->getPickupDate();
            $pickupStore = $extAttributes->getPickupStore();
            $pickupTime = $extAttributes->getPickupTime();
        }


        $enable = $this->dataHelper->isEnabled();

        if ($enable == 1 && $carrierCode == "storepickup" &&
            $methodCode == "storepickup") {
            $storeAddress = $this->dataHelper->getStoreAddress($pickupStore);
            $address->setCustomerAddressId(null);
            $address->setfirstname($storeAddress["firstname"]);
            $address->setLastname($storeAddress["lastname"]);
            $address->setStreet($storeAddress["address"]);
            $address->setCountryId($storeAddress["country"]);
            $address->setState($storeAddress["state"]);
            $address->setCity($storeAddress["city"]);
            $address->setRegion($storeAddress["region"]);
            $address->setPostcode($storeAddress["zipcode"]);
            $address->setRegionId($storeAddress["region_id"]);
            $address->setRegionCode($storeAddress["region_code"]);
            $address->setTelephone($storeAddress["phone_number"]);
            $address->setSaveInAddressBook(0);

        }

        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setShippingAddress($address);
        if(!$quote->getPickupDate()){
            $quote->setPickupDate($pickupDate);
        }
        $quote->setPickupStore($pickupStore);
        if(!$quote->getPickupTime()){
            $quote->setPickupTime($pickupTime);
        }
        if($extAttributes){
            $quote->setHearAboutId($extAttributes->getHearAboutId());
        }
        else{
            $quote->setHearAboutId('');
        }


        $quote = $this->quoteRepository->getActive($cartId);
        $quote = $this->prepareShippingAssignment($quote, $address, $carrierCode . '_' . $methodCode);
        $this->validateQuote($quote);
        $quote->setIsMultiShipping(false);

        if ($billingAddress) {
            $quote->setBillingAddress($billingAddress);
        }

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new InputException(__('Unable to save shipping information. Please check input data.'));
        }

        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress->getShippingRateByCode($shippingAddress->getShippingMethod())) {
            throw new NoSuchEntityException(
                __('Carrier with such method not found: %1, %2', $carrierCode, $methodCode)
            );
        }

        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));
        return $paymentDetails;
    }

    protected function validateQuote(\Magento\Quote\Model\Quote $quote)
    {
        if (0 == $quote->getItemsCount()) {
            throw new InputException(__('Shipping method is not applicable for empty cart'));
        }
    }

    private function prepareShippingAssignment(CartInterface $quote, AddressInterface $address, $method)
    {
        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->getCartExtensionFactory()->create();
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            $shippingAssignment = $this->getShippingAssignmentFactory()->create();
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->getShippingFactory()->create();
        }

        $shipping->setAddress($address);
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        return $quote->setExtensionAttributes($cartExtension);
    }

    private function getCartExtensionFactory()
    {
        if (!$this->cartExtensionFactory) {
            $this->cartExtensionFactory = ObjectManager::getInstance()->get(CartExtensionFactory::class);
        }
        return $this->cartExtensionFactory;
    }

    private function getShippingAssignmentFactory()
    {
        if (!$this->shippingAssignmentFactory) {
            $this->shippingAssignmentFactory = ObjectManager::getInstance()->get(ShippingAssignmentFactory::class);
        }
        return $this->shippingAssignmentFactory;
    }

    private function getShippingFactory()
    {
        if (!$this->shippingFactory) {
            $this->shippingFactory = ObjectManager::getInstance()->get(ShippingFactory::class);
        }
        return $this->shippingFactory;
    }


}