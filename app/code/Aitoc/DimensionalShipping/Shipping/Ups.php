<?php
namespace Aitoc\DimensionalShipping\Shipping;

class Ups extends \Magento\Ups\Model\Carrier {

	protected $cart;
	protected $packer;
	protected $packItem;
	protected $boxCollection;
	protected $helper;
	protected $dimensions;
	protected $packBox;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker\PackerFactory $packer,
        \Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker\TestItemFactory $packItem,
        \Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker\TestBoxFactory $packBox,
        \Magento\Checkout\Model\Cart $cart,
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
        \Aitoc\DimensionalShipping\Model\ResourceModel\Box\CollectionFactory $boxCollection,
        \Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions\CollectionFactory $dimensions,
        \Aitoc\DimensionalShipping\Helper\Data $helper,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Ups\Helper\Config $configHelper,
        \Magento\Framework\HTTP\ClientFactory $cf,
        array $data = []
	){
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
			$cf,
			$data
		);
		$this->cart = $cart;
		$this->packer = $packer->create();
		$this->packItem = $packItem;
		$this->boxCollection = $boxCollection;
		$this->helper = $helper;
		$this->dimensions = $dimensions;
		$this->packBox = $packBox;
	}

	protected function addBoxes(){
        $boxes = $this->boxCollection->create()->getItems();
        foreach ($boxes as $boxc) {
            $convertedUnitsBox = $this->helper->convertUnits($boxc, 'box');
            $box = $this->packBox->create([
                'reference'   => $convertedUnitsBox->getName(),
                'outerWidth'  => $convertedUnitsBox->getOuterWidth(),
                'outerLength' => $convertedUnitsBox->getOuterLength(),
                'outerDepth'  => $convertedUnitsBox->getOuterHeight(),
                'emptyWeight' => $convertedUnitsBox->getEmptyWeight(),
                'innerWidth'  => $convertedUnitsBox->getWidth(),
                'innerLength' => $convertedUnitsBox->getLength(),
                'innerDepth'  => $convertedUnitsBox->getHeight(),
                'maxWeight'   => $convertedUnitsBox->getWeight(),
                'boxId'       => $convertedUnitsBox->getId(),
                'weights' => [
                	1 => $boxc->getOneDayWeight(),
                	2 => $boxc->getTwoDayWeight(),
                	3 => $boxc->getThreeDayWeight(),
                	4 => $boxc->getFourDayWeight()
                ]
            ]);
            $this->packer->addBox($box);
        }
	}

	protected function getPackages(){
		$items = $this->cart->getQuote()->getItemsCollection();
		foreach($items as $item){
			if ($item->getProductType() == 'downloadable') {
                continue;
            }
			$options = $this->dimensions->create()->addFieldToFilter('product_id', $item->getProductId())->getFirstItem();
			$qty = 0;
	        while ($qty < $item->getQty()) {
	            $box = $this->packItem->create([
                    'description' => $item->getName(),
                    'width'       => $options->getWidth(),
                    'length'      => $options->getLength(),
                    'depth'       => $options->getHeight(),
                    'weight'      => $item->getProduct()->getWeight(),
                    'keepFlat'    => 0,
                    'orderItemId' => $item->getItemId()
	            ]);
	            $this->packer->addItem($box);
	            $qty++;
	        }
		}
		return $this->packer->pack();
	}

	protected function _getXmlQuotes() {
		$url = $this->getConfigData('gateway_xml_url');
        $this->setXMLAccessRequest();
        $base = $this->_xmlAccessRequest.'<?xml version="1.0"?>';
        $debugData['accessRequest'] = $this->filterDebugData($base);
        $rowRequest = $this->_rawRequest;
        if (self::USA_COUNTRY_ID == $rowRequest->getDestCountry()) {
            $destPostal = substr($rowRequest->getDestPostal(), 0, 5);
        } else {
            $destPostal = $rowRequest->getDestPostal();
        }
        $params = [
            'accept_UPS_license_agreement' => 'yes',
            '10_action' => $rowRequest->getAction(),
            '13_product' => $rowRequest->getProduct(),
            '14_origCountry' => $rowRequest->getOrigCountry(),
            '15_origPostal' => $rowRequest->getOrigPostal(),
            'origCity' => $rowRequest->getOrigCity(),
            'origRegionCode' => $rowRequest->getOrigRegionCode(),
            '19_destPostal' => $destPostal,
            '22_destCountry' => $rowRequest->getDestCountry(),
            'destRegionCode' => $rowRequest->getDestRegionCode(),
            '23_weight' => $rowRequest->getWeight(),
            '47_rate_chart' => $rowRequest->getPickup(),
            '48_container' => $rowRequest->getContainer(),
            '49_residential' => $rowRequest->getDestType(),
        ];
        if ($rowRequest->getIsReturn()) {
            $shipperCity = '';
            $shipperPostalCode = $params['19_destPostal'];
            $shipperCountryCode = $params['22_destCountry'];
            $shipperStateProvince = $params['destRegionCode'];
        } else {
            $shipperCity = $params['origCity'];
            $shipperPostalCode = $params['15_origPostal'];
            $shipperCountryCode = $params['14_origCountry'];
            $shipperStateProvince = $params['origRegionCode'];
        }
        if ($params['10_action'] == '4') {
            $params['10_action'] = 'Shop';
            $serviceCode = null;
        } else {
            $params['10_action'] = 'Rate';
            $serviceCode = $rowRequest->getProduct() ? $rowRequest->getProduct() : '';
        }
        $serviceDescription = $serviceCode ? $this->getShipmentByCode($serviceCode) : '';
        ob_start();
?>
<RatingServiceSelectionRequest xml:lang="en-US">
	<Request>
		<TransactionReference>
			<CustomerContext>Rating and Service</CustomerContext>
			<XpciVersion>1.0</XpciVersion>
		</TransactionReference>
		<RequestAction>Rate</RequestAction>
		<RequestOption><?php echo $params['10_action']; ?></RequestOption>
	</Request>
	<PickupType>
		<Code><?php echo $params['47_rate_chart']['code']; ?></Code>
		<Description><?php echo $params['47_rate_chart']['label']; ?></Description>
	</PickupType>
	<Shipment>
	  	<?php if($serviceCode != null): ?>
		<Service>
			<Code><?php echo $serviceCode; ?></Code>
			<Description><?php echo $serviceDescription; ?></Description>
		</Service>
	    <?php endif; ?>
		<Shipper>
	  		<?php if ($this->getConfigFlag('negotiated_active') && ($shipper = $this->getConfigData('shipper_number'))): ?>
			<ShipperNumber><?php echo $shipper; ?></ShipperNumber>
			<?php endif; ?>
	 		<Address>
				<City><?php echo $shipperCity; ?></City>
				<PostalCode><?php echo $shipperPostalCode; ?></PostalCode>
				<CountryCode><?php echo $shipperCountryCode; ?></CountryCode>
				<StateProvinceCode><?php echo $shipperStateProvince; ?></StateProvinceCode>
			</Address>
		</Shipper>
		<ShipTo>
			<Address>
				<PostalCode><?php echo $params['19_destPostal']; ?></PostalCode>
				<CountryCode><?php echo $params['22_destCountry']; ?></CountryCode>
				<ResidentialAddress><?php echo $params['49_residential']; ?></ResidentialAddress>
				<StateProvinceCode><?php echo $params['destRegionCode']; ?></StateProvinceCode>
				<?php if ($params['49_residential'] === '01'): ?>
				<ResidentialAddressIndicator><?php echo $params['49_residential']; ?></ResidentialAddressIndicator>
				<?php endif; ?>
			</Address>
		</ShipTo>
		<ShipFrom>
			<Address>
				<PostalCode><?php echo $params['15_origPostal']; ?></PostalCode>
				<CountryCode><?php echo $params['14_origCountry']; ?></CountryCode>
				<StateProvinceCode><?php echo $params['origRegionCode']; ?></StateProvinceCode>
			</Address>
		</ShipFrom>
		<?php
		$base .= ob_get_clean();
		$this->addBoxes();
		$transitTimes = $this->getTransitTimes();
		$results = [];
		foreach($transitTimes as $time => $codes){
			$packages = $this->getPackages();
			$next = $base;
			foreach($packages as $package){
				ob_start();
				?>
		<Package>
			<PackagingType> 
				<Code>00</Code> 
			</PackagingType> 
			<PackageWeight> 
				<UnitOfMeasurement> 
					<Code>LBS</Code> 
				</UnitOfMeasurement> 
				<Weight><?php echo ($package->getBox()->getMaxWeight() - $package->getRemainingWeight()) + $package->getBox()->getWeights()[$time]; ?></Weight> 
			</PackageWeight> 
			<Dimensions> 
				<Length><?php echo $package->getBox()->getOuterLength(); ?></Length> 
				<Width><?php echo $package->getBox()->getOuterWidth(); ?></Width> 
				<Height><?php echo $package->getBox()->getOuterDepth(); ?></Height> 
			</Dimensions>
		</Package>
				<?php
				$next .= ob_get_clean();
			}
			ob_start();
		?>	
		<?php if($this->getConfigFlag('negotiated_active')): ?>
		<RateInformation><NegotiatedRatesIndicator/></RateInformation>
		<?php endif; ?>
	</Shipment>
</RatingServiceSelectionRequest>
		<?php
			$next .= ob_get_clean();
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $next);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (bool)$this->getConfigFlag('mode_xml'));
            $response = curl_exec($ch);
            curl_close($ch);
            $result = simplexml_load_string($response);
          	foreach($result->RatedShipment as $shipment){
          		$code = (string)$shipment->Service->Code;
          		if(in_array($code, $codes)){
          			$price = (float)$shipment->TotalCharges->MonetaryValue;
          			$results[$code] = $price;
          		}
          	}
		}
		$res = $this->_rateFactory->create();

        if (empty($results)) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if (!isset($errorTitle)) {
                $errorTitle = __('Cannot retrieve shipping rates');
            }
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $res->append($error);
        } else {
            foreach ($results as $method => $price) {
                $rate = $this->_rateMethodFactory->create();
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $methodArr = $this->getShipmentByCode($method);
                $rate->setMethodTitle($methodArr);
                $rate->setCost($price);
                $rate->setPrice($price);
                $res->append($rate);
            }
        }
        return $res;
    }

    protected function getTransitTimes(){
    	$this->setXMLAccessRequest();
        $request = $this->_xmlAccessRequest.'<?xml version="1.0"?>';
        $raw = $this->_rawRequest;
    	$url = 'https://onlinetools.ups.com/ups.app/xml/TimeInTransit';
ob_start();
?>

<TimeInTransitRequest xml:lang="en-US"> 
	<Request>
		<TransactionReference> 
			<CustomerContext/> 
		</TransactionReference> 
		<RequestAction>TimeInTransit</RequestAction> 
	</Request> 
	<TransitFrom>
		<AddressArtifactFormat> 
			<PostcodePrimaryLow><?php echo ($raw->getIsReturn())?substr($raw->getDestPostal(), 0, 5):$raw->getOrigPostal(); ?></PostcodePrimaryLow> 
			<CountryCode><?php echo ($raw->getIsReturn())?substr($raw->getDestCountry(), 0, 5):$raw->getOrigCountry(); ?></CountryCode> 
		</AddressArtifactFormat> 
	</TransitFrom> 
	<TransitTo> 
		<AddressArtifactFormat> 
			<PostcodePrimaryLow><?php echo (!$raw->getIsReturn())?substr($raw->getDestPostal(), 0, 5):$raw->getOrigPostal(); ?></PostcodePrimaryLow> 
			<CountryCode><?php echo (!$raw->getIsReturn())?substr($raw->getDestCountry(), 0, 5):$raw->getOrigCountry(); ?></CountryCode> 
		</AddressArtifactFormat> 
	</TransitTo> 
	<PickupDate><?php echo date('Ymd', strtotime('+30 days')); ?></PickupDate> 
</TimeInTransitRequest>
<?php
		$request .= ob_get_clean();
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, (bool)$this->getConfigFlag('mode_xml'));
        $response = simplexml_load_string(curl_exec($ch));
        $mapToMethod = [
        	'1DM' => '14',
            '1DA' => '01',
            '2DA' => '02',
            '3DS' => '12',
            'GND' => '03' 
        ];
        $return = [];
        if($response && isset($response->TransitResponse)){
	        foreach($response->TransitResponse->ServiceSummary as $summary){
	        	$days = (int)$summary->EstimatedArrival->BusinessTransitDays;
	        	$code = (string)$summary->Service->Code;
	        	if(isset($mapToMethod[$code])){
	        		if(!isset($return[$days])){
	        			$return[$days] = [];
	        		}
	        		$return[$days][] = $mapToMethod[$code];
	        	}
	        }
	        if(!isset($return[3])){
	        	$return[3] = [];
	        	
	        }
	        if(!in_array('12', $return[3])){
	        	$return[3][] = '12';
	        }
	    }
		return $return;
    }

}