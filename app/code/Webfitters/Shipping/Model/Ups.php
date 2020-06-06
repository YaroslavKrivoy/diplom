<?php
namespace Webfitters\Shipping\Model;

class Ups extends \Magento\Ups\Model\Carrier {

    private $httpClientFactory;
    private $scopeConfig;
    protected $adminOrder;
    protected $state;
    protected $frontOrder;

	public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Xml\Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Ups\Helper\Config $configHelper,
        \Magento\Framework\HTTP\ClientFactory $httpClientFactory,
        \Magento\Sales\Model\AdminOrder\Create $adminOrder,
        \Magento\Checkout\Model\Session $frontOrder,
        \Magento\Framework\App\State $state,
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $localeFormat,
       		$configHelper,
        	$httpClientFactory,
            $data
        );
        $this->adminOrder = $adminOrder;
        $this->frontOrder = $frontOrder;
        $this->scopeConfig = $scopeConfig;
        $this->state = $state;
        $this->httpClientFactory = $httpClientFactory;
    }
    
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request){
        $this->setRequest($request);
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }
        $this->setRequest($request);
        $this->_result = $this->_getQuotes();
        $this->_updateFreeMethodQuote($request);
        return $this->getResult();
    }

    protected function getError(){
        $result = $this->_rateFactory->create();
        $error = $this->_rateErrorFactory->create();
        $error->setCarrier('ups');
        $error->setCarrierTitle($this->getConfigData('title'));
        if (!isset($errorTitle)) {
            $errorTitle = __('Cannot retrieve shipping rates');
        }
        $error->setErrorMessage($this->getConfigData('specificerrmsg'));
        $result->append($error);
        return $result;
    }

    protected function _getXmlQuotes() {
        //Not the cleanest, but I don't really feel like recompiling...
        
        $data = $this->_rawRequest;
        $all = $this->getAllRates();;
        if(!$all){
            return $this->getError();
        }
        $days = [
            1 => $this->scopeConfig->getValue('webfitters_shipping/general/one_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), 
            2 => $this->scopeConfig->getValue('webfitters_shipping/general/two_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), 
            3 => $this->scopeConfig->getValue('webfitters_shipping/general/three_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE), 
            4 => $this->scopeConfig->getValue('webfitters_shipping/general/four_day_weight', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ];
        $allowed = explode(",", $this->getConfigData('allowed_methods'));
        $compiled = [];
        $done = [];
        foreach($all->RatedShipment as $rate){
            if(in_array($rate->Service->getCode(), $allowed) && !in_array($rate->Service->getCode(), $done)){
                if(isset($rate->TimeInTransit) && isset($rate->TimeInTransit->ServiceSummary) && $rate->TimeInTransit->ServiceSummary->getEstimatedArrival()) {
                    $time = $rate->TimeInTransit->ServiceSummary->getEstimatedArrival()->getBusinessDaysInTransit();
                } else {
                    $time = 3;
                }
                if($time > 3) {
                    continue;
                }
                if($this->state->getAreaCode() === 'adminhtml'){
                    $weight = 0;
                    $items = $this->adminOrder->getQuote()->getAllItems();
                    foreach($items as $item){
                        $weight += ($item->getQtyWeight() > 0)?$item->getQtyWeight():$item->getRowWeight();
                    }
                } else {
                    $weight = $this->_rawRequest->getWeight();
                }
                $weight = ((isset($days[$time]))?$days[$time]:$days[3]) + $data->getWeight();
                $actual = $this->getRate($weight, $rate->Service->getCode());
                if($actual){
                    $actual->RatedShipment[0]->TimeInTransit = $time;
                    $actual->RatedShipment[0]->Code = $rate->Service->getCode();
                    $compiled[] = $actual->RatedShipment[0];
                }
                $done[] = $rate->Service->getCode();
            }
        }
        if(count($compiled) == 0){
            return $this->getError();
        }
        $result = $this->_rateFactory->create();
        foreach($compiled as $method){
            $rate = $this->_rateMethodFactory->create();
            $rate->setCarrier('ups');
            $rate->setCarrierTitle($this->getConfigData('title'));
            $rate->setMethod((string)$method->Service->getCode());
            $methodArr = $this->getShipmentByCode((string)$method->Service->getCode());
            $rate->setMethodTitle($methodArr);
            if($this->state->getAreaCode() === 'adminhtml' && $this->adminOrder->getQuote()->getUsesContainerFee()){
                if($this->adminOrder->getQuote()->getContainerFeeAdditive()){
                    $rate->setPrice((float)$method->TotalCharges->MonetaryValue + (float)$this->adminOrder->getQuote()->getContainerFee());
                    $rate->setCost((float)$method->TotalCharges->MonetaryValue + (float)$this->adminOrder->getQuote()->getContainerFee());
                } else {
                    $rate->setPrice((float)$this->adminOrder->getQuote()->getContainerFee());
                    $rate->setCost((float)$this->adminOrder->getQuote()->getContainerFee());
                }
            } else {
                $rate->setPrice((float)$method->TotalCharges->MonetaryValue);
                $rate->setCost((float)$method->TotalCharges->MonetaryValue);
            }
            $result->append($rate);
        }
        if($this->state->getAreaCode() === 'adminhtml'){
            $quote = $this->adminOrder->getQuote();
            $quote->setUpsData(json_encode($compiled));
            $quote->save();
        } else {
            $quote = $this->frontOrder->getQuote();
            $quote->setUpsData(json_encode($compiled));
        }
        return $result;
    }

    protected function getAllRates(){
        $rate = new \Ups\RateTimeInTransit($this->getConfigData('access_license_number'), $this->getConfigData('username'), $this->getConfigData('password'));
        $data = $this->_rawRequest;
        try { 
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $directory = $om->get('\Magento\Framework\Filesystem\DirectoryList');
            $log = fopen($directory->getRoot().'/var/log/ups.log', 'a+');
            $shipment = $this->getShipment($data->getWeight());
            ob_start();
            print_r($shipment);
            fwrite($log, '--- '.date('Y-m-d H:i:s').' Request ---'.PHP_EOL.PHP_EOL.ob_get_clean().PHP_EOL.PHP_EOL);
            $rates = $rate->shopRatesTimeInTransit($shipment);
            ob_start();
            print_r($rate);
            fwrite($log, '--- '.date('Y-m-d H:i:s').' Response ---'.PHP_EOL.PHP_EOL.ob_get_clean().PHP_EOL.PHP_EOL);
            fclose($log);

            return $rates;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getRate($weight, $code){
        $rate = new \Ups\Rate($this->getConfigData('access_license_number'), $this->getConfigData('username'), $this->getConfigData('password'));
        $data = $this->_rawRequest;
        try {
            $shipment = $this->getShipment($weight);
            $service = new \Ups\Entity\Service();
            $service->setCode($code);
            $shipment->setService($service);
            return $rate->getRate($shipment);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getShipment($weight){
        $data = $this->_rawRequest;
        $shipment = new \Ups\Entity\Shipment();
        $shipperAddress = $shipment->getShipper()->getAddress();
        $shipperAddress->setPostalCode($data->getOrigPostal());
        $address = new \Ups\Entity\Address();
        $address->setStateProvinceCode($data->getOrigRegionCode());
        $address->setPostalCode($data->getOrigPostal());
        $shipFrom = new \Ups\Entity\ShipFrom();
        $shipFrom->setAddress($address);
        $shipment->setShipFrom($shipFrom);
        $shipTo = $shipment->getShipTo();
        $shipToAddress = $shipTo->getAddress();
        $shipToAddress->setStateProvinceCode($data->getDestRegionCode());
        $shipToAddress->setCity($this->_request->getDestCity());
        $shipToAddress->setPostalCode($this->getDestination());
        if ($this->getConfigData('dest_type') == 'RES') {
            $shipToAddress->setResidentialAddressIndicator('RES');
        }
        $packages = floor($weight / 150);
        $mod = $weight - ($packages * 150);
        if($mod > 0){
            $ins = $data->getValue() / ($packages + 1);
        } else {
            $ins = $data->getValue() / $packages;
        }
        for($i = 0; $i < $packages; $i++){
            $package = new \Ups\Entity\Package();
            $package->getPackagingType()->setCode($data['container']);
            $package->getPackageWeight()->setWeight(150);
            $weightUnit = new \Ups\Entity\UnitOfMeasurement;
            $weightUnit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_LBS);
            $package->getPackageWeight()->setUnitOfMeasurement($weightUnit);
            $insurance = new \Ups\Entity\InsuredValue();
            $insurance->setCurrencyCode('USD');
            $insurance->setMonetaryValue($ins);
            $options = new \Ups\Entity\PackageServiceOptions();
            $options->setInsuredValue($insurance);
            $package->setPackageServiceOptions($options);
            $shipment->addPackage($package);
        }
        if($mod > 0){
            $package = new \Ups\Entity\Package();
            $package->getPackagingType()->setCode($data['container']);
            $package->getPackageWeight()->setWeight($mod);
            $weightUnit = new \Ups\Entity\UnitOfMeasurement;
            $weightUnit->setCode(\Ups\Entity\UnitOfMeasurement::UOM_LBS);
            $package->getPackageWeight()->setUnitOfMeasurement($weightUnit);
            $insurance = new \Ups\Entity\InsuredValue();
            $insurance->setCurrencyCode('USD');
            $insurance->setMonetaryValue($ins);
            $options = new \Ups\Entity\PackageServiceOptions();
            $options->setInsuredValue($insurance);
            $package->setPackageServiceOptions($options);
            $shipment->addPackage($package);
        }
        $delivery = new \Ups\Entity\DeliveryTimeInformation();
        $delivery->setPackageBillType(\Ups\Entity\DeliveryTimeInformation::PBT_NON_DOCUMENT);
        $pickup = new \Ups\Entity\Pickup();
        $pickup->setDate(\Carbon\Carbon::now()->format('Ymd'));
        $pickup->setTime(\Carbon\Carbon::now()->format('His'));
        $shipment->setDeliveryTimeInformation($delivery);
        if ($this->getConfigFlag('negotiated_active') && $this->getConfigData('shipper_number')) {
            $shipper = new \Ups\Entity\Shipper();
            $shipper->setShipperNumber($this->getConfigData('shipper_number'));
            $shipment->setShipper($shipper);
            $shipment->showNegotiatedRates();
        }
        return $shipment;
    }

    protected function getDestination(){
        $data = $this->_rawRequest;
        if (self::USA_COUNTRY_ID == $data->getDestCountry()) {
            $destination = substr($data->getDestPostal(), 0, 5);
        } else {
            $destination = $data->getDestPostal();
        }
        return $destination;
    }

    public function requestToShipment($request) {
        try {
        $response = new \Magento\Framework\DataObject();
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new LocalizedException(__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }

        $itemNames = [];
        foreach($request->getPackages() as $package){
            foreach ($package['items'] as $item) {
                $itemNames[] = $item['name'];
            }
        }
        $shipment = new \Ups\Entity\Shipment();
        $shipper = $shipment->getShipper();
        $shipper->setShipperNumber($this->getConfigData('shipper_number'));
        $shipper->setName($request->getShipperContactCompanyName());
        $shipper->setAttentionName($request->getShipperContactPersonName());
        $shipperAddress = $shipper->getAddress();
        $shipperAddress->setAddressLine1($request->getShipperAddressStreet1());
        $shipperAddress->setPostalCode($request->getShipperAddressPostalCode());
        $shipperAddress->setCity($request->getShipperAddressCity());
        $shipperAddress->setStateProvinceCode($request->getShipperAddressStateOrProvinceCode());
        $shipperAddress->setCountryCode($request->getShipperAddressCountryCode());
        $shipper->setAddress($shipperAddress);
        $shipper->setEmailAddress($request->getShipperEmail()); 
        $shipper->setPhoneNumber($request->getShipperContactPhoneNumber());
        $shipment->setShipper($shipper);
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1($request->getRecipientAddressStreet1());
        $address->setAddressLine2($request->getRecipientAddressStreet2());
        $address->setAddressLine3($request->getRecipientAddressStreet3());
        $address->setPostalCode($request->getRecipientAddressPostalCode());
        $address->setCity($request->getRecipientAddressCity());
        $address->setStateProvinceCode($request->getRecipientAddressRegionCode());
        $address->setCountryCode($request->getRecipientAddressCountryCode());
        if ($this->getConfigData('dest_type') == 'RES') {
            $address->setResidentialAddressIndicator('RES');
        }
        $shipTo = new \Ups\Entity\ShipTo();
        $shipTo->setAddress($address);
        $shipTo->setCompanyName($request->getRecipientContactCompanyName() ? $request->getRecipientContactCompanyName() : 'N/A');
        $shipTo->setAttentionName($request->getRecipientContactPersonName());
        $shipTo->setEmailAddress($request->getRecipientEmail()); 
        $shipTo->setPhoneNumber($request->getRecipientContactPhoneNumber());
        $shipment->setShipTo($shipTo);
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1($request->getShipperAddressStreet1());
        $address->setPostalCode($request->getShipperAddressPostalCode());
        $address->setCity($request->getShipperAddressCity());
        $address->setStateProvinceCode($request->getShipperAddressStateOrProvinceCode());  
        $address->setCountryCode($request->getShipperAddressCountryCode());
        $shipFrom = new \Ups\Entity\ShipFrom();
        $shipFrom->setAddress($address);
        $shipFrom->setName($request->getShipperContactCompanyName());
        $shipFrom->setAttentionName($shipFrom->getName());
        $shipFrom->setCompanyName($shipFrom->getName());
        $shipFrom->setEmailAddress($request->getShipperEmail());
        $shipFrom->setPhoneNumber($request->getShipperContactPhoneNumber());
        $shipment->setShipFrom($shipFrom);
        $service = new \Ups\Entity\Service();
        $service->setCode($request->getShippingMethod());
        $service->setDescription($service->getName());
        $shipment->setService($service);
        $shipment->setDescription(substr(implode(' ', $itemNames), 0, 35));
        $delivery = false;
        foreach($request->getPackages() as $package){
            $params = (object)$package['params'];
            $items = $package['items'];
            $ins = 0;
            $names = [];
            foreach($items as $item){
                $ins += $item['price'] * $item['qty'];
                $names[] = $item['name'];
            }
            $package = new \Ups\Entity\Package();
            $package->getPackagingType()->setCode($params->container);
            $package->getPackageWeight()->setWeight($params->weight);
            $unit = new \Ups\Entity\UnitOfMeasurement();
            $unit->setCode(($params->weight_units == 'POUND')?\Ups\Entity\UnitOfMeasurement::UOM_LBS:\Ups\Entity\UnitOfMeasurement::UOM_KGS);
            $package->getPackageWeight()->setUnitOfMeasurement($unit);
            $packageServiceOptions = new \Ups\Entity\PackageServiceOptions();
            $insurance = new \Ups\Entity\InsuredValue();
            $insurance->setMonetaryValue($ins);
            $insurance->setCurrencyCode('USD');
            $packageServiceOptions->setInsuredValue($insurance);
            if($params->delivery_confirmation && $this->_getDeliveryConfirmationLevel($request->getRecipientAddressCountryCode()) == self::DELIVERY_CONFIRMATION_PACKAGE){
                $confirmation = new \Ups\Entity\DeliveryConfirmation();
                $confirmation->setDcisType($request->getPackageParams()->getDeliveryConfirmation());
                $packageServiceOptions->setDeliveryConfirmation($confirmation);
                $delivery = true;
            }
            $package->setPackageServiceOptions($packageServiceOptions);
            $dimensions = new \Ups\Entity\Dimensions();
            $dimensions->setHeight($params->height);
            $dimensions->setWidth($params->width);
            $dimensions->setLength($params->length);
            $unit = new \Ups\Entity\UnitOfMeasurement();
            $unit->setCode(($params->dimension_units ==  'INCH')?\Ups\Entity\UnitOfMeasurement::UOM_IN:\Ups\Entity\UnitOfMeasurement::UOM_CM);
            $dimensions->setUnitOfMeasurement($unit);
            $package->setDimensions($dimensions);
            $package->setDescription(substr(implode(' ', $names), 0, 35));
            if ($this->_isUSCountry($request->getRecipientAddressCountryCode()) && $this->_isUSCountry($request->getShipperAddressCountryCode())) {
                if ($request->getReferenceData()) {
                    $referenceData = $request->getReferenceData() . $request->getPackageId();
                } else {
                    $referenceData = 'Order #'.$request->getOrderShipment()->getOrder()->getIncrementId().' P'.$request->getPackageId();
                }
                $referenceNumber = new \Ups\Entity\ReferenceNumber();
                $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
                $referenceNumber->setValue($referenceData);
                $package->setReferenceNumber($referenceNumber);
            }
            $shipment->addPackage($package);
        }
        $shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object)array('AccountNumber' => $this->getConfigData('shipper_number'))));
        $rateInformation = new \Ups\Entity\RateInformation();
        $rateInformation->setNegotiatedRatesIndicator(1);
        $shipment->setRateInformation($rateInformation);
        if($delivery && $this->_getDeliveryConfirmationLevel($request->getRecipientAddressCountryCode()) == self::DELIVERY_CONFIRMATION_SHIPMENT){
            $serviceOptions = new \Ups\Entity\ShipmentServiceOptions();
            $confirmation = new \Ups\Entity\DeliveryConfirmation();
            $confirmation->setDcisType($request->getPackageParams()->getDeliveryConfirmation());
            $serviceOptions->setDeliveryConfirmation($confirmation);
            $shipment->setServiceOptions($serviceOptions);
        }
        
            $api = new \Ups\Shipping($this->getConfigData('access_license_number'), $this->getConfigData('username'), $this->getConfigData('password'), true); 
            $confirm = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $shipment);
            if ($confirm) {
                $accept = $api->accept($confirm->ShipmentDigest);
                $data = [];
                if(is_array($accept->PackageResults)){
                    foreach($accept->PackageResults as $package){
                        $data[] = [
                            'tracking_number' => $package->TrackingNumber,
                            'label_content' => base64_decode($package->LabelImage->GraphicImage)
                        ];
                    }
                } else {
                    $data[] = [
                        'tracking_number' => $accept->PackageResults->TrackingNumber,
                        'label_content' => base64_decode($accept->PackageResults->LabelImage->GraphicImage)
                    ];
                }
                $response->setInfo($data);
            } else {
                $response->setErrors(['Unable to create shipping labels']);
            }
        } catch (\Exception $e) {
            $response->setErrors([$e->getMessage()]);
        }
        return $response;
    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request){
        
    }

    public function getTracking($trackings){
        if(!is_array($trackings)){
            $trackings = [$trackings];
        }
        $tracking = new \Ups\Tracking($this->getConfigData('access_license_number'), $this->getConfigData('username'), $this->getConfigData('password'));
        if (!$this->_result) {
            $this->_result = $this->_trackFactory->create();
        }
        foreach($trackings as $code){
            try {
                $result = [];
                $shipment = $tracking->track($code);
                $result['service'] = $shipment->Service->Description;
                $shipped = $this->getFullDate($shipment->PickupDate, '000000');
                $result['shippeddate'] = $shipped->format('Y-m-d');
                if(isset($shipment->ScheduledDeliveryDate)){
                    $delivery = $this->getFullDate($shipment->ScheduledDeliveryDate, '000000');
                    $result['deliverydate'] = $delivery->format('Y-m-d');
                }
                $result['weight'] = $shipment->ShipmentWeight->Weight.' '.$shipment->ShipmentWeight->UnitOfMeasurement->Code;
                $result['deliveryto'] = $this->parseAddress($shipment->ShipTo->Address);
                $result['progressdetail'] = [];
                if($shipment->Package->Activity){
                    if(isset($shipment->Package->Activity[0])){
                        $current = $shipment->Package->Activity[0];
                        $result['status'] = $current->Status->StatusType->Description;
                        $result['deliverylocation'] = $this->parseAddress($current->ActivityLocation->Address);
                        if(!isset($result['deliverydate'])){
                            $delivery = $this->getFullDate($current->Date, $current->Time);
                            $result['deliverydate'] = $delivery->format('Y-m-d');
                        }
                    }
                    foreach($shipment->Package->Activity as $activity){
                        $date = $this->getFullDate($activity->Date, $activity->Time);
                        $result['progressdetail'][] = [
                            'activity' => $activity->Status->StatusType->Description,
                            'deliverydate' => $date->format('Y-m-d'),
                            'deliverytime' => $date->format('H:i:s'),
                            'deliverylocation' => $this->parseAddress($activity->ActivityLocation->Address)
                        ];
                    }
                }
                $final = $this->_trackStatusFactory->create();
                $final->setCarrier('ups');
                $final->setCarrierTitle($this->getConfigData('title'));
                $final->setTracking($code);
                $final->addData($result);
                $this->_result->append($final);
            } catch(\Exception $e){dump($e);}
        }
        return $this->_result;
    }

    private function parseAddress($address){
        $return = [];
        if(isset($address->City)){
            $return[] = $address->City;
        }
        if(isset($address->StateProvinceCode)){
            $return[] = $address->StateProvinceCode;
        }
        if(isset($address->PostalCode)){
            $return[] = $address->PostalCode;
        }
        if(isset($address->CountryCode)){
            $return[] = $address->CountryCode;
        }
        return implode(', ', $return);
    }

    private function getFullDate($date, $time){
        return \Carbon\Carbon::parse($this->parseDate($date).' '.$this->parseTime($time))->addHours(31);
    }

    private function parseDate($date){
        return substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, -2, 2);
        
    }

    private function parseTime($time){
        return substr($time, 0, 2).':'.substr($time, 2, 2).':'.substr($time, -2, 2);
    }

}