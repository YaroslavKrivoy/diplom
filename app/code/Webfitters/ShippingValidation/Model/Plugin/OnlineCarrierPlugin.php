<?php
namespace Webfitters\ShippingValidation\Model\Plugin;

class OnlineCarrierPlugin {

	protected $errors;
	protected $config;
	protected $address;
	protected $rateRequest;
	protected $request;
	protected $order;

	public function __construct(
		\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $errors,
		\Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Customer\Model\Address $address,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Sales\Model\OrderFactory $order,
		\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
	) {
		$this->address = $address;
		$this->config = $config;
		$this->errors = $errors;
		$this->rateRequest = $rateRequest;
		$this->request = $request;
		$this->order = $order;
	}

	public function aroundProccessAdditionalValidation(\Magento\Shipping\Model\Carrier\AbstractCarrierOnline $carrier, \Closure $proceed){
		$data = file_get_contents('php://input');
		if($data == '' && !$this->request->getParam('order_id')){
			return $this->error($carrier, 'No shipping information passed.');
		}
		if($this->request->getParam('order_id')){
			$order = $this->order->create()->load($this->request->getParam('order_id'));
			$c = $order->getShippingAddress()->getCity();
			$city = $order->getShippingAddress()->getPostcode();
			$state = $order->getShippingAddress()->getRegion();
			$country = $order->getShippingAddress()->getCountryId();
		} else {
			$data = json_decode($data);
			if(!$data){	
				return $carrier;
			}
			if(isset($data->billingAddress)){
				return $proceed($this->rateRequest);
			}
			if(!$data || (!isset($data->address) && !isset($data->addressId) && !isset($data->addressInformation))){
				return $this->error($carrier, 'We can\'t seem to find the address you entered. Please double check.');
			}
			if(isset($data->address->postcode) && $data->address->postcode == null){

			}
			if(isset($data->addressId)){
				$address = $this->address->load($data->addressId);
				$c = $address->getCity();
				$city = $address->getPostcode();
				$state = $address->getRegion();
				$country = $address->getCountryId();
			} else if(isset($data->addressInformation)){
				if(isset($data->addressInformation->shipping_address)){
					$address = $data->addressInformation->shipping_address;
				} else {
					$address = $data->addressInformation->address;
				}
				$c = ((isset($address->city))?$address->city:'');
				$city = ((isset($address->postcode))?$address->postcode:'');
				$state = ((isset($address->region))?$address->region:'');
				$country = ((isset($address->countryId))?$address->countryId:'');
			} else {
				$c = ((isset($data->address->city))?$data->address->city:'');
				$city = ((isset($data->address->postcode))?$data->address->postcode:'');
				$state = ((isset($data->address->region))?$data->address->region:'');
				$country = ((isset($data->address->country_id))?$data->address->country_id:'');
			}
		}
		if($city == '' || !$city || $state == '' || !$state || $c == '' || !$c){
			return $this->error($carrier, 'Please enter a city, state and zipcode for rates or if logged in one of your stored addresses.');
		}
		$validation = json_decode(@file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?components=administrative_area:'.urlencode($state).'|postal_code:'.urlencode($city).'|country:'.urlencode($country).'&key='.trim($this->config->getValue('shipping_validation/general/google_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))));
		if(!$validation || $validation->status == 'INVALID_REQUEST' || $validation->status == 'ZERO_RESULTS'){
			return $this->error($carrier, 'We can\'t seem to find the address you entered. Please double check.');
		}

		return $carrier;
	}

	protected function error($carrier, $message){
        if ($carrier->getConfigData('showmethod')) {
            $error = $this->errors->create();
            $error->setCarrier($carrier->getCarrierCode());
            $error->setCarrierTitle($carrier->getConfigData('title'));
            $error->setErrorMessage($message);
            return $error;
        }
        return false;
    }

}