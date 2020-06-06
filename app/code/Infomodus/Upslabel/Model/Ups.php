<?php

namespace Infomodus\Upslabel\Model;

class Ups
{
    public $_handy;
    public $accessLicenseNumber;
    public $userId;
    public $password;
    public $shipperNumber;

    public $packages;
    public $weightUnits;
    public $packageWeight;

    public $unitOfMeasurement;
    public $length;
    public $width;
    public $height;

    public $customerContext;
    public $shipperName;
    public $shipperPhoneNumber;
    public $shipperAddressLine1;
    public $shipperCity;
    public $shipperStateProvinceCode;
    public $shipperPostalCode;
    public $shipperCountryCode;
    public $shipmentDescription;
    public $shipperAttentionName;

    public $shiptoCompanyName;
    public $shiptoAttentionName;
    public $shiptoPhoneNumber;
    public $shiptoAddressLine1;
    public $shiptoAddressLine2;
    public $shiptoCity;
    public $shiptoStateProvinceCode;
    public $shiptoPostalCode;
    public $shiptoCountryCode;
    public $residentialAddress;

    public $shipfromCompanyName;
    public $shipfromAttentionName;
    public $shipfromPhoneNumber;
    public $shipfromAddressLine1;
    public $shipfromAddressLine2;
    public $shipfromCity;
    public $shipfromStateProvinceCode;
    public $shipfromPostalCode;
    public $shipfromCountryCode;

    public $serviceCode;
    public $serviceDescription;
    public $shipmentDigest;

    public $trackingNumber;
    public $shipmentIdentificationNumber;
    public $graphicImage;
    public $htmlImage;

    public $codYesNo;
    public $currencyCode;
    public $codMonetaryValue;
    public $codFundsCode;
    public $invoicelinetotal;
    public $carbonNeutral;
    public $testing;
    public $shipmentcharge = 0;
    public $qvn = 0;
    public $qvnCode = 0;
    public $qvnEmailShipper = '';
    public $qvnEmailShipto = '';
    public $qvnLang = '';
    public $adult = 0;
    public $upsAccount = 0;
    public $accountData;
    public $saturdayDelivery;
    public $movement_reference_number;
    public $internationalInvoice;
    public $internationalDescription;
    public $internationalComments;
    public $internationalInvoicenumber;
    public $internationalReasonforexport;
    public $internationalTermsofshipment;
    public $internationalPurchaseordernumber;
    public $internationalProducts;
    public $internationalSoldTo;
    public $internationalInvoicedate;
    public $declaration_statement = '';
    public $returnServiceCode = 8;

    /* Pickup */
    public $ratePickupIndicator;
    public $closeTime;
    public $readyTime;
    public $pickupDateYear;
    public $pickupDateMonth;
    public $pickupDateDay;
    public $alternateAddressIndicator;
    public $quantity;
    public $destinationCountryCode;
    public $containerCode;
    public $weight;
    public $overweightIndicator;
    public $paymentMethod;
    public $specialInstruction;
    public $referenceNumber;
    public $notification;
    public $confirmationEmailAddress;
    public $undeliverableEmailAddress;
    public $room;
    public $floor;
    public $urbanization;
    public $residential;
    public $pickupPoint;
    /* END Pickup */

    /* Access Point */
    public $accesspoint = 0;
    public $accesspointType;
    public $accesspointName;
    public $accesspointAtname;
    public $accesspointAppuid;
    public $accesspointStreet;
    public $accesspointStreet1;
    public $accesspointStreet2;
    public $accesspointCity;
    public $accesspointProvincecode;
    public $accesspointPostal;
    public $accesspointCountry;
    public $accesspoint_COD = true;
    /* Access Point */

    public $storeId = null;
    public $negotiatedRates = 0;
    public $ratesTax = 0;
    public $printerType = "GIF";

    public function getShip($type = 'shipment')
    {
        $this->_handy->_conf->createMediaFolders();
        $path = $this->_handy->_conf->getBaseDir('media') . '/upslabel/label/';
        $pathXml = $this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/';
        $this->customerContext = $this->shipperName;
        $this->printerType = $this->_handy->_conf->getStoreConfig('upslabel/printing/printer', $this->storeId);
        $validate = $this->_handy->_conf->getStoreConfig('upslabel/shipping/validate', $this->storeId) == 1 ? 'validate' : 'nonvalidate';
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->userId . "</UserId>
<Password>" . $this->password . "</Password>
</AccessRequest>
<?xml version=\"1.0\"?>
<ShipmentConfirmRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
      <CustomerContext>" . $this->customerContext . "</CustomerContext>
      <XpciVersion/>
    </TransactionReference>
    <RequestAction>ShipConfirm</RequestAction>
    <RequestOption>" . $validate . "</RequestOption>
  </Request>
  <Shipment>";
        if ($this->_handy->_conf->getStoreConfig('upslabel/ratepayment/negotiatedratesindicator', $this->storeId) == 1) {
            $data .= "
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }

        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }

        $data .= $this->setShipper() . $this->setShipTo($type) . $this->setShipFrom($type);
        if ($this->shiptoCountryCode != $this->shipfromCountryCode) {
            $paymentTag = 'ItemizedPaymentInformation';
            $data .= "<" . $paymentTag . ">";
            if ($this->upsAccount != 1) {
                $data .= "<ShipmentCharge><Type>01</Type><BillShipper>";
                $data .= "<AccountNumber>" . $this->shipperNumber . "</AccountNumber>";
                $data .= "</BillShipper></ShipmentCharge>";
                if ($this->shipmentcharge === 'shipper') {
                    $data .= "
                <ShipmentCharge>
                <Type>02</Type>
                  <BillShipper>
                    <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
                  </BillShipper></ShipmentCharge>";
                }
            } else {
                $data .= "<ShipmentCharge><Type>01</Type><BillThirdParty>
                    <BillThirdPartyShipper>
                        <AccountNumber>" . $this->accountData->getAccountnumber() . "</AccountNumber>
                        <ThirdParty>
                            <Address>
                                <PostalCode>" . $this->accountData->getPostalcode() . "</PostalCode>
                                <CountryCode>" . $this->accountData->getCountry() . "</CountryCode>
                            </Address>
                        </ThirdParty>
                    </BillThirdPartyShipper>
                </BillThirdParty></ShipmentCharge>";
            }
            $data .= "
                </" . $paymentTag . ">
            ";
        } else {
            $paymentTag = 'PaymentInformation';
            $data .= "<" . $paymentTag . ">";
            if ($this->upsAccount != 1) {
                $data .= "<Prepaid>
        <BillShipper>";
                /*if ($this->accesspoint == 1 && $this->accesspoint_type == '02') {
                    $data .= "<AlternatePaymentMethod>01</AlternatePaymentMethod>";
                } else {*/
                $data .= "<AccountNumber>" . $this->shipperNumber . "</AccountNumber>";
                /*}*/
                $data .= "</BillShipper>
      </Prepaid>";
            } else {
                $data .= "<BillThirdParty>
                    <BillThirdPartyShipper>
                        <AccountNumber>" . $this->accountData->getAccountnumber() . "</AccountNumber>
                        <ThirdParty>
                            <Address>
                                <PostalCode>" . $this->accountData->getPostalcode() . "</PostalCode>
                                <CountryCode>" . $this->accountData->getCountry() . "</CountryCode>
                            </Address>
                        </ThirdParty>
                    </BillThirdPartyShipper>
                </BillThirdParty>";
            }
            $data .= "
                </" . $paymentTag . ">
            ";
        }
        $data .= "<Service>
      <Code>" . $this->serviceCode . "</Code>
      <Description>" . $this->serviceDescription . "</Description>
    </Service>";
        if ($this->shiptoCountryCode != $this->shipfromCountryCode && $this->movement_reference_number != '') {
            $data .= "<MovementReferenceNumber>" . $this->movement_reference_number . "</MovementReferenceNumber>";
        }
        if ($this->shiptoCountryCode != $this->shipfromCountryCode
            || ($this->shiptoCountryCode == $this->shipfromCountryCode
                && $this->shiptoCountryCode != 'US' && $this->shiptoCountryCode != 'PR')) {
            $data .= "<ReferenceNumber>";
            if ($this->_handy->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode', $this->storeId) == 1) {
                $data .= "<BarCodeIndicator/>";
            }
            $data .= "<Code>" . $this->packages[0]['packagingreferencenumbercode'] . "</Code>
		<Value>" . $this->packages[0]['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
            if (isset($this->packages[0]['packagingreferencenumbercode2'])) {
                $data .= "<ReferenceNumber>";
                if ($this->_handy->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode2', $this->storeId) == 1) {
                    $data .= "<BarCodeIndicator/>";
                }
                $data .= "<Code>" . $this->packages[0]['packagingreferencenumbercode2'] . "</Code>
		<Value>" . $this->packages[0]['packagingreferencenumbervalue2'] . "</Value>
	  </ReferenceNumber>";
            }
        }
        foreach ($this->packages as $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR')
                && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>";
                if ($this->_handy->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode', $this->storeId) == 1) {
                    $data .= "<BarCodeIndicator/>";
                }
                $data .= "<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
                if (isset($pv['packagingreferencenumbercode2'])) {
                    $data .= "<ReferenceNumber>";
                    $data .= "<Code>" . $pv['packagingreferencenumbercode2'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue2'] . "</Value>
	  </ReferenceNumber>";
                }
            }
            $data .= array_key_exists('additionalhandling', $pv) && $pv['additionalhandling'] == 1 ? '<AdditionalHandling/>' : '';
            if ((isset($pv['length'])
                && isset($pv['width'])
                && isset($pv['height'])
                && strlen($pv['length']) > 0
                && strlen($pv['width']) > 0
                && strlen($pv['height']) > 0)
            ) {
                $data .= "<Dimensions><UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                $data .= "</UnitOfMeasurement>";
                $data .= "<Length>" . round($pv['length'], 0) . "</Length>
<Width>" . round($pv['width'], 0) . "</Width>
<Height>" . round($pv['height'], 0) . "</Height>";
                $data .= "</Dimensions>";
            }
            $data .= "<PackageWeight><UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? (float)str_replace(',', '.', $pv['weight']) : '';
            $weight = ceil($weight * 10) / 10;
            $weight = round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1);
            $data .= "</UnitOfMeasurement>
        <Weight>" . ($weight < 0.1 ? 0.1 : $weight) . "</Weight>";
            $data .= $this->largePackageIndicator($pv);
            $data .= "</PackageWeight><PackageServiceOptions>";
            if (isset($pv['insuredmonetaryvalue']) && $pv['insuredmonetaryvalue'] > 0) {
                $data .= "<InsuredValue>
                <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                <MonetaryValue>" . (isset($pv['insuredmonetaryvalue']) ? $pv['insuredmonetaryvalue'] : 0)
                    . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            if ($pv['cod'] == 1
                && ($this->shiptoCountryCode == 'US'
                    || $this->shiptoCountryCode == 'PR'
                    || $this->shiptoCountryCode == 'CA')
                && ($this->shipfromCountryCode == 'US'
                    || $this->shipfromCountryCode == 'PR'
                    || $this->shipfromCountryCode == 'CA')) {
                $this->printerType = "GIF";
                if ($this->accesspoint != 1) {
                    $data .= "
              <COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $pv['codfundscode'] . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $this->currencyCode . "</CurrencyCod>
                      <MonetaryValue>" . (isset($pv['codmonetaryvalue']) ? $pv['codmonetaryvalue'] : 0) . "</MonetaryValue>
                  </CODAmount>
              </COD>";
                } else {
                    $data .= "
              <AccessPointCOD>
                <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                <MonetaryValue>" . (isset($pv['codmonetaryvalue']) ? $pv['codmonetaryvalue'] : 0) . "</MonetaryValue>
              </AccessPointCOD>";
                }
            }
            if ($this->isAdult('P')) {
                $data .= "<DeliveryConfirmation><DCISType>" . $this->adult . "</DCISType></DeliveryConfirmation>";
            }
            $data .= "</PackageServiceOptions></Package>";
        }
        $data .= "<ShipmentServiceOptions>";
        if ($this->codYesNo == 1
            && $this->shiptoCountryCode != 'US'
            && $this->shiptoCountryCode != 'PR'
            && $this->shiptoCountryCode != 'CA'
            && $this->shipfromCountryCode != 'US'
            && $this->shipfromCountryCode != 'PR'
            && $this->shipfromCountryCode != 'CA') {
            $this->printerType = "GIF";
            if ($this->accesspoint != 1) {
                $data .= "<COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $this->codFundsCode . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                      <MonetaryValue>" . $this->codMonetaryValue . "</MonetaryValue>
                  </CODAmount>
              </COD>";
            } elseif ($this->accesspoint_COD === true) {
                $data .= "<AccessPointCOD>
                      <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                      <MonetaryValue>" . $this->codMonetaryValue . "</MonetaryValue>
              </AccessPointCOD>";
            }
        }
        if ($this->isAdult('S')) {
            $data .= "<DeliveryConfirmation><DCISType>" . $this->adult . "</DCISType></DeliveryConfirmation>";
        }
        if ($this->carbonNeutral == 1) {
            $data .= "<UPScarbonneutralIndicator/>";
        }

        $qvnCodesAdmin = explode(',', $this->_handy->_conf->getStoreConfig('upslabel/quantum/qvn_code_admin', $this->storeId));
        if ($this->qvn == 1) {
            $emailUndelivery = 0;
            foreach ($this->qvnCode as $qvncode) {
                if ($qvncode != 2 && $qvncode != 5) {
                    $data .= "<Notification>
            <NotificationCode>" . $qvncode . "</NotificationCode>
            <EMailMessage>";
                    if (strlen($this->qvnEmailShipper) > 0 && in_array($qvncode, $qvnCodesAdmin)) {
                        $data .= "<EMailAddress>" . $this->qvnEmailShipper . "</EMailAddress>";
                    }
                    if (strlen($this->qvnEmailShipto) > 0) {
                        $data .= "<EMailAddress>" . $this->qvnEmailShipto . "</EMailAddress>";
                    }
                    if (strlen($this->qvnEmailShipper) > 0 && $emailUndelivery == 0) {
                        $data .= "<UndeliverableEMailAddress>" . $this->qvnEmailShipper
                            . "</UndeliverableEMailAddress>";
                        $emailUndelivery = 1;
                    }
                    $data .= "</EMailMessage>";
                    if (strlen($this->qvnLang) > 4) {
                        $qvnLang = explode(":", $this->qvnLang);
                        $data .= "<Locale><Language>" . $qvnLang[0]
                            . "</Language><Dialect>" . $qvnLang[1] . "</Dialect></Locale>";
                    }
                    $data .= "</Notification>";
                    if ($this->accesspoint == 1) {
                        break;
                    }
                }
            }
        }
        if ($this->accesspoint == 1) {
            $data .= "<Notification>
            <NotificationCode>012</NotificationCode>
            <EMailMessage>";
            if (strlen($this->qvnEmailShipper) > 0 && count($qvnCodesAdmin) > 0) {
                $data .= "<EMailAddress>" . $this->qvnEmailShipper . "</EMailAddress>";
            }
            if (strlen($this->qvnEmailShipto) > 0) {
                $data .= "<EMailAddress>" . $this->qvnEmailShipto . "</EMailAddress>";
            }
            $data .= "</EMailMessage>";
            if ($this->qvnLang && strlen($this->qvnLang) > 4) {
                $qvnLang = explode(":", $this->qvnLang);
                $data .= "<Locale><Language>" . $qvnLang[0]
                    . "</Language><Dialect>" . $qvnLang[1] . "</Dialect></Locale>";
            } else {
                $data .= "<Locale><Language>ENG</Language><Dialect>GB</Dialect></Locale>";
            }
            $data .= "</Notification>";
            $data .= "<Notification>
            <NotificationCode>013</NotificationCode>
            <EMailMessage>";
            if (strlen($this->qvnEmailShipper) > 0) {
                $data .= "<EMailAddress>" . $this->qvnEmailShipper . "</EMailAddress>";
            }
            if (strlen($this->qvnEmailShipto) > 0) {
                $data .= "<EMailAddress>" . $this->qvnEmailShipto . "</EMailAddress>";
            }
            $data .= "</EMailMessage>";
            if (strlen($this->qvnLang) > 4) {
                $qvnLang = explode(":", $this->qvnLang);
                $data .= "<Locale><Language>" . $qvnLang[0] . "</Language><Dialect>" . $qvnLang[1] . "</Dialect></Locale>";
            } else {
                $data .= "<Locale><Language>ENG</Language><Dialect>GB</Dialect></Locale>";
            }
            $data .= "</Notification>";
        }
        $data .= $this->saturdayDelivery;
        if ($this->internationalInvoice == 1
            && $this->shiptoCountryCode != $this->shipfromCountryCode
            && count($this->internationalProducts) > 0) {
            $data .= "<InternationalForms>
<FormType>01</FormType>
<AdditionalDocumentIndicator></AdditionalDocumentIndicator>";
            foreach ($this->internationalProducts as $interproduct) {
                $data .= "<Product>
            <Description>" . $this->_handy->_conf->escapeXML($interproduct['description']) . "</Description>
            <OriginCountryCode>" . $this->_handy->_conf->escapeXML($interproduct['country_code']) . "</OriginCountryCode>
            <Unit>
            <Number>" . $this->_handy->_conf->escapeXML($interproduct['qty']) . "</Number>
            <Value>" . $this->_handy->_conf->escapeXML($interproduct['amount']) . "</Value>
            <UnitOfMeasurement>
            <Code>" . $this->_handy->_conf->escapeXML($interproduct['unit_of_measurement']) . "</Code>";
                if (isset($interproduct['unit_of_measurementdesc'])
                    && strlen($interproduct['unit_of_measurementdesc']) > 0) {
                    $data .= "<Description>" . $this->_handy->_conf->escapeXML($interproduct['unit_of_measurementdesc']) . "</Description>";
                }
                $data .= "</UnitOfMeasurement>
            </Unit>
            <CommodityCode>" . $this->_handy->_conf->escapeXML($interproduct['commoditycode']) . "</CommodityCode>
            <PartNumber>" . $this->_handy->_conf->escapeXML($interproduct['partnumber']) . "</PartNumber>";
                if (isset($interproduct['scheduleB_number'], $interproduct['scheduleB_unit']) && $interproduct['scheduleB_number'] != '' && $interproduct['scheduleB_unit'] != '') {
                    $data .= "<ScheduleB>
                    <Number>" . $interproduct['scheduleB_number'] . "</Number>
                    <Quantity>" . $interproduct['qty'] . "</Quantity>
                    <UnitOfMeasurement>
                    <Code>" . $interproduct['scheduleB_unit'] . "</Code>
                    <Description>" . $this->_handy->objectManager->get('Infomodus\Upslabel\Model\Config\SchedulebUnitofmeasurement')->getScheduleUnitName($interproduct['scheduleB_unit']) . "</Description>
                    </UnitOfMeasurement>
                    </ScheduleB>";
                }
                $data .= "</Product>";
            }
            $data .= "<PurchaseOrderNumber>" . $this->internationalPurchaseordernumber . "</PurchaseOrderNumber>
            <TermsOfShipment>" . $this->internationalTermsofshipment . "</TermsOfShipment>
            <ReasonForExport>" . $this->internationalReasonforexport . "</ReasonForExport>
            <Comments>" . $this->internationalComments . "</Comments>
            <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
            <InvoiceNumber>" . $this->internationalInvoicenumber . "</InvoiceNumber>
            <InvoiceDate>" . $this->internationalInvoicedate . "</InvoiceDate>";
            if (trim($this->declaration_statement) != '') {
                $data .= "<DeclarationStatement>" . $this->declaration_statement . "</DeclarationStatement>";
            }
            $data .= "
            </InternationalForms>";
            /*<DeclarationStatement>qww we ete rt</DeclarationStatement>*/
        }
        $data .= "</ShipmentServiceOptions>";
        if (strlen($this->invoicelinetotal) > 0
            && ($this->shiptoCountryCode == 'US'
                || $this->shiptoCountryCode == 'PR'
                || $this->shiptoCountryCode == 'CA')
            && ($this->shipfromCountryCode == 'US'
                || $this->shipfromCountryCode == 'PR'
                || $this->shipfromCountryCode == 'CA')
            && $this->shiptoCountryCode != $this->shipfromCountryCode) {
            $data .= "<InvoiceLineTotal>
                          <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                          <MonetaryValue>" . $this->invoicelinetotal . "</MonetaryValue>
              </InvoiceLineTotal>";
        }
        if ($this->accesspoint == 1) {
            $data .= "<ShipmentIndicationType>
            <Code>" . $this->accesspointType . "</Code>
            </ShipmentIndicationType>
            <AlternateDeliveryAddress>
                <Name>" . $this->accesspointName . "</Name>
                <AttentionName>" . $this->accesspointAtname . "</AttentionName>
                <Address>";
            $addressline1 = str_split($this->accesspointStreet, 35);
            foreach ($addressline1 as $addressline) {
                $data .= "<AddressLine1>" . $addressline . "</AddressLine1>";
            }
            if ($this->accesspointStreet1 != "" && $this->accesspointStreet1 != "undefined") {
                $data .= "<AddressLine2>" . $this->accesspointStreet1 . "</AddressLine2>";
            }
            if ($this->accesspointStreet2 != "" && $this->accesspointStreet2 != "undefined") {
                $data .= "<AddressLine3>" . $this->accesspointStreet2 . "</AddressLine3>";
            }
            $data .= "<City>" . $this->accesspointCity . "</City>";
            if ($this->shiptoCountryCode == "US" || $this->shiptoCountryCode == "CA") {
                $data .= "<StateProvinceCode>" . $this->accesspointProvincecode . "</StateProvinceCode>";
            }
            $data .= "<PostalCode>" . $this->accesspointPostal . "</PostalCode>
                    <CountryCode>" . $this->accesspointCountry . "</CountryCode>
                </Address>
                <UPSAccessPointID>" . $this->accesspointAppuid . "</UPSAccessPointID>
            </AlternateDeliveryAddress>
           ";
        }
        if ($this->internationalInvoice == 1
            && $this->shiptoCountryCode != $this->shipfromCountryCode) {
            /*if ($this->internationalSoldTo == 'shipper') {
                $data .= "<SoldTo>
                <CompanyName>" . $this->shipperName . "</CompanyName>
                <AttentionName>" . $this->shipperAttentionName . "</AttentionName>
                <PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
                <Address>
                    <AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
                    <City>" . $this->shipperCity . "</City>
                    <StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
                    <PostalCode>" . $this->shipperPostalCode . "</PostalCode>
                    <CountryCode>" . $this->shipperCountryCode . "</CountryCode>
                </Address>
            </SoldTo>";
            } else {*/
            $data .= "<SoldTo>
                <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
                <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
                <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
                <Address>
                    <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>
                    <City>" . $this->shiptoCity . "</City>
                    <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
                    <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
                    <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
                </Address>
            </SoldTo>";
            /*}*/
        }
        $data .= "</Shipment>
<LabelSpecification>
    <LabelPrintMethod>
      <Code>" . $this->printerType . "</Code>
    </LabelPrintMethod>
    ";
        if ($this->printerType != "GIF") {
            $data .= "<LabelStockSize>
                <Height>4</Height>
                <Width>" . $this->_handy->_conf->getStoreConfig('upslabel/printing/termal_width', $this->storeId) . "</Width>
            </LabelStockSize>";
        }
        $data .= "
    <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
    <LabelImageFormat>
      <Code>" . $this->printerType . "</Code>
    </LabelImageFormat>
  </LabelSpecification>
</ShipmentConfirmRequest>";

        file_put_contents($pathXml . "ShipConfirmRequest.xml", $data);
        $errorRequest = $data;
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $curl = $this->_handy->_conf;
        $curl->testing = !$this->testing;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/ShipConfirm', $data);
        if (!$curl->error) {
            file_put_contents($pathXml . "ShipConfirmResponse.xml", $result);
            $errorResponse = $result;
            $xml = simplexml_load_string($result);
            if ($xml->Response->ResponseStatusCode[0] == 1) {
                if ($xml->NegotiatedRates) {
                    $shiplabelprice = $xml->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue[0];
                    $shiplabelcurrency = $xml->NegotiatedRates->NetSummaryCharges->GrandTotal->CurrencyCode[0];
                } else {
                    $shiplabelprice = $xml->ShipmentCharges->TotalCharges->MonetaryValue[0];
                    $shiplabelcurrency = $xml->ShipmentCharges->TotalCharges->CurrencyCode[0];
                }
                $this->shipmentDigest = $xml->ShipmentDigest[0];
                $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->userId . "</UserId>
<Password>" . $this->password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<ShipmentAcceptRequest>
<Request>
<TransactionReference>
<CustomerContext>" . $this->customerContext . "</CustomerContext>
<XpciVersion>1.0001</XpciVersion>
</TransactionReference>
<RequestAction>ShipAccept</RequestAction>
</Request>
<ShipmentDigest>" . $this->shipmentDigest . "</ShipmentDigest>
</ShipmentAcceptRequest>";
                file_put_contents($pathXml . "ShipAcceptRequest.xml", $data);
                $curl->testing = !$this->testing;
                $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/ShipAccept', $data);

                if (!$curl->error) {
                    file_put_contents($pathXml . "ShipAcceptResponse.xml", $result);
                    $xml = simplexml_load_string($result);
                    $this->shipmentIdentificationNumber = $xml->ShipmentResults[0]->ShipmentIdentificationNumber[0];
                    $arrResponsXML = [];
                    $i = 0;
                    foreach ($xml->ShipmentResults[0]->PackageResults as $resultXML) {
                        $arrResponsXML[$i]['trackingnumber'] = $resultXML->TrackingNumber[0];
                        $arrResponsXML[$i]['graphicImage'] = base64_decode($resultXML->LabelImage[0]->GraphicImage[0]);
                        $arrResponsXML[$i]['type_print'] = ($this->printerType != 'GIF' ? $resultXML->LabelImage[0]->LabelImageFormat[0]->Code[0] : $this->printerType);
                        $file = fopen($path . 'label' . $arrResponsXML[$i]['trackingnumber'] . '.' . strtolower($arrResponsXML[$i]['type_print']), 'w');
                        fwrite($file, $arrResponsXML[$i]['graphicImage']);
                        fclose($file);
                        if ($arrResponsXML[$i]['type_print'] == "GIF" && isset($resultXML->LabelImage[0]->HTMLImage[0])) {
                            $arrResponsXML[$i]['htmlImage'] = base64_decode($resultXML->LabelImage[0]->HTMLImage[0]);
                            file_put_contents($path . $arrResponsXML[$i]['trackingnumber'] . ".html", $arrResponsXML[$i]['htmlImage']);
                            file_put_contents($pathXml . "HTML_image.html", $arrResponsXML[$i]['htmlImage']);
                        }
                        $i += 1;
                    }

                    $interInvoice = null;
                    if ($this->internationalInvoice == 1) {
                        if (isset($xml->ShipmentResults[0]->Form->Image->GraphicImage)) {
                            if (is_array($xml->ShipmentResults[0]->Form->Image->GraphicImage)) {
                                $interInvoice = $xml->ShipmentResults[0]->Form->Image->GraphicImage[0];
                            } else {
                                $interInvoice = $xml->ShipmentResults[0]->Form->Image->GraphicImage;
                            }
                        }
                    }

                    $turnInPage = null;
                    if ($xml->ShipmentResults[0]->CODTurnInPage
                        && $xml->ShipmentResults[0]->CODTurnInPage->Image->GraphicImage) {
                        $turnInPage = $xml->ShipmentResults[0]->CODTurnInPage->Image->GraphicImage;
                    }

                    if ($this->codMonetaryValue > 999) {
                        $htmlHVReport = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 11">
<meta name=Originator content="Microsoft Word 11">
<link rel=File-List href="sample%20UPS%20CONTROL%20LOG_files/filelist.xml">
<title>UPS CONTROL LOG </title>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>xlm8zff</o:Author>
  <o:LastAuthor>xlm8zff</o:LastAuthor>
  <o:Revision>2</o:Revision>
  <o:TotalTime>2</o:TotalTime>
  <o:Created>2010-09-27T12:53:00Z</o:Created>
  <o:LastSaved>2010-09-27T12:53:00Z</o:LastSaved>
  <o:Pages>1</o:Pages>
  <o:Words>116</o:Words>
  <o:Characters>662</o:Characters>
  <o:Company>UPS</o:Company>
  <o:Lines>5</o:Lines>
  <o:Paragraphs>1</o:Paragraphs>
  <o:CharactersWithSpaces>777</o:CharactersWithSpaces>
  <o:Version>11.9999</o:Version>
 </o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:SpellingState>Clean</w:SpellingState>
  <w:GrammarState>Clean</w:GrammarState>
  <w:PunctuationKerning/>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:Compatibility>
   <w:BreakWrappedTables/>
   <w:SnapToGridInCell/>
   <w:WrapTextWithPunct/>
   <w:UseAsianBreakRules/>
   <w:DontGrowAutofit/>
  </w:Compatibility>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
 </w:LatentStyles>
</xml><![endif]-->
<style>
<!--
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin:0in;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	mso-bidi-font-size:12.0pt;
	font-family:Arial;
	mso-fareast-font-family:"Times New Roman";}
span.GramE
	{mso-style-name:"";
	mso-gram-e:yes;}
@page Section1
	{size:8.5in 11.0in;
	margin:1.0in 1.25in 1.0in 1.25in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:"Table Normal";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0in 5.4pt 0in 5.4pt;
	mso-para-margin:0in;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;}
</style>
<![endif]-->
</head>
<body lang=EN-US style=\'tab-interval:.5in\'>

<div class=Section1>

<p class=MsoNormal>UPS CONTROL <span class=GramE>LOG</span></p>

<p class=MsoNormal>DATE: ' . date('d') . ' ' . date('M') . ' ' . date('Y') . ' UPS SHIPPER NO. ' . $this->shipperNumber . ' </p>
<br />
<br />
<p class=MsoNormal>TRACKING # PACKAGE ID REFRENCE NUMBER DECLARED VALUE
CURRENCY </p>
<p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
</p>
<br /><br />
<p class=MsoNormal>' . $this->trackingNumber . ' <span class=GramE>' . $this->packages[0]['packagingreferencenumbervalue'] . ' ' . round($this->codMonetaryValue, 2) . '</span> ' . $this->currencyCode . ' </p>
<br /><br />
<p class=MsoNormal>Total Number of Declared Value Packages = 1 </p>
<p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
</p>
<br /><br />
<p class=MsoNormal>RECEIVED BY_________________________PICKUP
TIME__________________PKGS_______ </p>
</div>
</body>
</html>';
                        file_put_contents($path . "HVR" . $this->shipmentIdentificationNumber . ".html", $htmlHVReport);
                    }
                    return [
                        'arrResponsXML' => $arrResponsXML,
                        'digest' => '' . $this->shipmentDigest . '',
                        'shipidnumber' => '' . $this->shipmentIdentificationNumber . '',
                        'price' => ['currency' => $shiplabelcurrency, 'price' => $shiplabelprice],
                        'inter_invoice' => $interInvoice,
                        'turn_in_page' => $turnInPage
                    ];
                } else {
                    return ['error' => "CURL server error", 'request' => "Request Error", 'response' => "Response Error"];
                }
            } else {
                $errorss = $xml->Response->Error[0];
                $this->_handy->_conf->log($errorss->ErrorDescription);
                return ['error' => $errorss->ErrorDescription, 'request' => $errorRequest, 'response' => $errorResponse];
            }
        } else {
            return ['error' => "CURL server error", 'request' => "Request Error", 'response' => "Response Error"];
        }
    }

    public function getShipFrom($type = 'refund')
    {
        /* if(is_dir($filename)){} */
        $this->_handy->_conf->createMediaFolders();
        $path = $this->_handy->_conf->getBaseDir('media') . '/upslabel/label/';
        $pathXml = $this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/';
        $this->customerContext = $this->shipperName;
        $validate = $this->_handy->_conf->getStoreConfig('upslabel/shipping/validate' /*multistore*/, $this->storeId /*multistore*/) == 1 ? 'validate' : 'nonvalidate';
        $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'><AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber><UserId>" . $this->userId . "</UserId><Password>" . $this->password . "</Password></AccessRequest>
        <?xml version=\"1.0\"?>
        <ShipmentConfirmRequest xml:lang=\"en-US\">
          <Request>
            <TransactionReference>
              <CustomerContext>" . $this->customerContext . "</CustomerContext>
              <XpciVersion/>
            </TransactionReference>
            <RequestAction>ShipConfirm</RequestAction>
            <RequestOption>" . $validate . "</RequestOption>
          </Request>
          <LabelSpecification>
            <LabelPrintMethod>
              <Code>GIF</Code>
              <Description>gif file</Description>
            </LabelPrintMethod>
            <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
            <LabelImageFormat>
              <Code>GIF</Code>
              <Description>gif</Description>
            </LabelImageFormat>
          </LabelSpecification>
          <Shipment>";
        if ($this->_handy->_conf->getStoreConfig('upslabel/ratepayment/negotiatedratesindicator', $this->storeId) == 1) {
            $data .= "<RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        $data .= "<ShipmentServiceOptions>";
        if ($this->returnServiceCode == 8) {
            $data .= "<LabelDelivery>
                        <LabelLinksIndicator />
                    </LabelDelivery>";
        }
        if ($this->carbonNeutral == 1) {
            $data .= "<UPScarbonneutralIndicator/>";
        }
        if ($this->qvn == 1) {
            $emailUndelivery = 0;
            foreach ($this->qvnCode as $qvncode) {
                if ($qvncode == 2 || $qvncode == 5) {
                    $data .= "<Notification>
            <NotificationCode>" . $qvncode . "</NotificationCode>
            <EMailMessage>";
                    if (strlen($this->qvnEmailShipper) > 0) {
                        $data .= "<EMailAddress>" . $this->qvnEmailShipper . "</EMailAddress>";
                    }
                    if (strlen($this->qvnEmailShipto) > 0) {
                        $data .= "<EMailAddress>" . $this->qvnEmailShipto . "</EMailAddress>";
                    }
                    if (strlen($this->qvnEmailShipper) > 0 && $emailUndelivery == 0) {
                        $data .= "<UndeliverableEMailAddress>" . $this->qvnEmailShipper . "</UndeliverableEMailAddress>";
                        $emailUndelivery = 1;
                    }
                    $data .= "</EMailMessage>";
                    if (strlen($this->qvnLang) > 4) {
                        $qvnLang = explode(":", $this->qvnLang);
                        $data .= "<Locale><Language>" . $qvnLang[0] . "</Language><Dialect>" . $qvnLang[1] . "</Dialect></Locale>";
                    }
                    $data .= "</Notification>";
                }
            }
        }
        $data .= $this->saturdayDelivery . "</ShipmentServiceOptions>";
        $data .= "<ReturnService><Code>" . $this->returnServiceCode . "</Code></ReturnService>";
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= $this->setShipper() . $this->setShipFrom($type) . $this->setShipTo($type) . "
             <PaymentInformation>
              <Prepaid>
                <BillShipper>
                  <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
                </BillShipper>
              </Prepaid>
            </PaymentInformation>
            <Service>
              <Code>" . $this->serviceCode . "</Code>
              <Description>" . $this->serviceDescription . "</Description>
            </Service>";
        if ($this->shiptoCountryCode != $this->shipfromCountryCode || ($this->shiptoCountryCode == $this->shipfromCountryCode && $this->shiptoCountryCode != 'US' && $this->shiptoCountryCode != 'PR')) {
            $data .= "<ReferenceNumber>";
            if ($this->_handy->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode', $this->storeId) == 1) {
                $data .= "<BarCodeIndicator></BarCodeIndicator>";
            }
            $data .= "<Code>" . $this->packages[0]['packagingreferencenumbercode'] . "</Code>
		<Value>" . $this->packages[0]['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
            if (isset($this->packages[0]['packagingreferencenumbercode2'])) {
                $data .= "<ReferenceNumber>";
                if ($this->_handy->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode2', $this->storeId) == 1) {
                    $data .= "<BarCodeIndicator></BarCodeIndicator>";
                }
                $data .= "<Code>" . $this->packages[0]['packagingreferencenumbercode2'] . "</Code>
		<Value>" . $this->packages[0]['packagingreferencenumbervalue2'] . "</Value>
	  </ReferenceNumber>";
            }
        }
        foreach ($this->packages as $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>";
                if ($this->_handy->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode', $this->storeId) == 1) {
                    $data .= "<BarCodeIndicator></BarCodeIndicator>";
                }
                $data .= "<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
                if (isset($pv['packagingreferencenumbercode2'])) {
                    $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode2'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue2'] . "</Value>
	  </ReferenceNumber>";
                }
            }
            $data .= array_key_exists('additionalhandling', $pv) && $pv['additionalhandling'] == 1 ? '<AdditionalHandling/>' : '';
            if ((isset($pv['length']) && isset($pv['width']) && isset($pv['height']) && strlen($pv['length']) > 0 && strlen($pv['width']) > 0 && strlen($pv['height']) > 0)
            ) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                $data .= "</UnitOfMeasurement>";
                $data .= "<Length>" . round($pv['length'], 0) . "</Length>
<Width>" . round($pv['width'], 0) . "</Width>
<Height>" . round($pv['height'], 0) . "</Height>";
                $data .= "</Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? (float)str_replace(',', '.', $pv['weight']) : '';
            $weight = ceil($weight * 10) / 10;
            $weight = round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1);
            $data .= "</UnitOfMeasurement>
        <Weight>" . $weight . "</Weight>";
            $data .= $this->largePackageIndicator($pv);
            $data .= "</PackageWeight>
      <PackageServiceOptions>";
            if ($pv['insuredmonetaryvalue'] > 0) {
                $data .= "<InsuredValue>
                <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                <MonetaryValue>" . (isset($pv['insuredmonetaryvalue']) ? $pv['insuredmonetaryvalue'] : '') . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
            break;
        }
        $data .= "
          </Shipment>
        </ShipmentConfirmRequest>
        ";

        file_put_contents($pathXml . "ShipConfirmRefundRequest.xml", $data);
        $errorRequest = $data;

        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }

        $curl = $this->_handy->_conf;
        $curl->testing = !$this->testing;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/ShipConfirm', $data);

        if (!$curl->error) {
            file_put_contents($pathXml . "ShipConfirmRefundResponse.xml", $result);
            $errorResponse = $result;
        } else {
            return $result;
        }
        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            if ($xml->NegotiatedRates) {
                $shiplabelprice = $xml->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue[0];
                $shiplabelcurrency = $xml->NegotiatedRates->NetSummaryCharges->GrandTotal->CurrencyCode[0];
            } else {
                $shiplabelprice = $xml->ShipmentCharges->TotalCharges->MonetaryValue[0];
                $shiplabelcurrency = $xml->ShipmentCharges->TotalCharges->CurrencyCode[0];
            }
            $this->shipmentDigest = $xml->ShipmentDigest[0];
            $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'><AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber><UserId>" . $this->userId . "</UserId><Password>" . $this->password . "</Password></AccessRequest><?xml version=\"1.0\" ?><ShipmentAcceptRequest><Request><TransactionReference><CustomerContext>" . $this->customerContext . "</CustomerContext><XpciVersion>1.0001</XpciVersion></TransactionReference><RequestAction>ShipAccept</RequestAction></Request><ShipmentDigest>" . $this->shipmentDigest . "</ShipmentDigest></ShipmentAcceptRequest>";

            file_put_contents($pathXml . "ShipAcceptRefundRequest.xml", $data);
            $curl->testing = !$this->testing;
            $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/ShipAccept', $data);
            if (!$curl->error) {
                file_put_contents($pathXml . "ShipAcceptRefundResponse.xml", $result);
            } else {
                return $result;
            }
            $xml = simplexml_load_string($result);
            $this->shipmentIdentificationNumber = $xml->ShipmentResults[0]->ShipmentIdentificationNumber[0];
            $i = 0;
            $arrResponsXML = [];
            foreach ($xml->ShipmentResults[0]->PackageResults as $resultXML) {
                $arrResponsXML[$i]['trackingnumber'] = $resultXML->TrackingNumber[0];
                $htmlUrlUPS = $this->_handy->_conf->getBaseUrl('media').'/upslabel/label';

                if ($resultXML->LabelImage && $resultXML->LabelImage->GraphicImage) {
                    $arrResponsXML[$i]['type_print'] = "GIF";
                    $imgName = base64_decode($resultXML->LabelImage->GraphicImage[0]);
                    $this->htmlImage = base64_decode($resultXML->LabelImage->HTMLImage[0]);
                    file_put_contents($path . 'label' . $arrResponsXML[$i]['trackingnumber'] . '.gif', $imgName);
                    if($resultXML->LabelImage->PDF417[0]){
                        $pdf417 = base64_decode($resultXML->LabelImage->PDF417[0]);
                        file_put_contents($path . 'label' . $arrResponsXML[$i]['trackingnumber'] . 'pdf417.gif', $pdf417);
                    }

                    $this->htmlImage = preg_replace('/<img\s*?src="\./is', '<img src="' . $htmlUrlUPS, $this->htmlImage);
                    file_put_contents($path . $arrResponsXML[$i]['trackingnumber'] . ".html", $this->htmlImage);
                    file_put_contents($pathXml . "HTML_image.html", $this->htmlImage);
                    $arrResponsXML[$i]['labelname'] = 'label' . $arrResponsXML[$i]['trackingnumber'] . '.gif';
                } else {
                    if ($xml->ShipmentResults[$i]->LabelURL) {
                        $arrResponsXML[$i]['type_print'] = "link";
                        $arrResponsXML[$i]['graphicImage'] = $xml->ShipmentResults[$i]->LabelURL[0];
                    } else {
                        $arrResponsXML[$i]['type_print'] = "virtual";
                    }
                }

                $i += 1;
            }

            if ($this->codMonetaryValue > 999) {
                $htmlHVReport = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">

        <head>
        <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
        <meta name=ProgId content=Word.Document>
        <meta name=Generator content="Microsoft Word 11">
        <meta name=Originator content="Microsoft Word 11">
        <link rel=File-List href="sample%20UPS%20CONTROL%20LOG_files/filelist.xml">
        <title>UPS CONTROL LOG </title>
        <!--[if gte mso 9]><xml>
         <o:DocumentProperties>
          <o:Author>xlm8zff</o:Author>
          <o:LastAuthor>xlm8zff</o:LastAuthor>
          <o:Revision>2</o:Revision>
          <o:TotalTime>2</o:TotalTime>
          <o:Created>2010-09-27T12:53:00Z</o:Created>
          <o:LastSaved>2010-09-27T12:53:00Z</o:LastSaved>
          <o:Pages>1</o:Pages>
          <o:Words>116</o:Words>
          <o:Characters>662</o:Characters>
          <o:Company>UPS</o:Company>
          <o:Lines>5</o:Lines>
          <o:Paragraphs>1</o:Paragraphs>
          <o:CharactersWithSpaces>777</o:CharactersWithSpaces>
          <o:Version>11.9999</o:Version>
         </o:DocumentProperties>
        </xml><![endif]--><!--[if gte mso 9]><xml>
         <w:WordDocument>
          <w:SpellingState>Clean</w:SpellingState>
          <w:GrammarState>Clean</w:GrammarState>
          <w:PunctuationKerning/>
          <w:ValidateAgainstSchemas/>
          <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
          <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
          <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
          <w:Compatibility>
           <w:BreakWrappedTables/>
           <w:SnapToGridInCell/>
           <w:WrapTextWithPunct/>
           <w:UseAsianBreakRules/>
           <w:DontGrowAutofit/>
          </w:Compatibility>
          <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
         </w:WordDocument>
        </xml><![endif]--><!--[if gte mso 9]><xml>
         <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
         </w:LatentStyles>
        </xml><![endif]-->
        <style>
        <!--
         /* Style Definitions */
         p.MsoNormal, li.MsoNormal, div.MsoNormal
        	{mso-style-parent:"";
        	margin:0in;
        	margin-bottom:.0001pt;
        	mso-pagination:widow-orphan;
        	font-size:10.0pt;
        	mso-bidi-font-size:12.0pt;
        	font-family:Arial;
        	mso-fareast-font-family:"Times New Roman";}
        span.GramE
        	{mso-style-name:"";
        	mso-gram-e:yes;}
        @page Section1
        	{size:8.5in 11.0in;
        	margin:1.0in 1.25in 1.0in 1.25in;
        	mso-header-margin:.5in;
        	mso-footer-margin:.5in;
        	mso-paper-source:0;}
        div.Section1
        	{page:Section1;}
        -->
        </style>
        <!--[if gte mso 10]>
        <style>
         /* Style Definitions */
         table.MsoNormalTable
        	{mso-style-name:"Table Normal";
        	mso-tstyle-rowband-size:0;
        	mso-tstyle-colband-size:0;
        	mso-style-noshow:yes;
        	mso-style-parent:"";
        	mso-padding-alt:0in 5.4pt 0in 5.4pt;
        	mso-para-margin:0in;
        	mso-para-margin-bottom:.0001pt;
        	mso-pagination:widow-orphan;
        	font-size:10.0pt;
        	font-family:"Times New Roman";
        	mso-ansi-language:#0400;
        	mso-fareast-language:#0400;
        	mso-bidi-language:#0400;}
        </style>
        <![endif]-->
        </head>
        <body lang=EN-US style=\'tab-interval:.5in\'>

        <div class=Section1>

        <p class=MsoNormal>UPS CONTROL <span class=GramE>LOG</span></p>

        <p class=MsoNormal>DATE: ' . date('d') . ' ' . date('M') . ' ' . date('Y') . ' UPS SHIPPER NO. ' . $this->shipperNumber . ' </p>
        <br />
        <br />
        <p class=MsoNormal>TRACKING # PACKAGE ID REFRENCE NUMBER DECLARED VALUE
        CURRENCY </p>
        <p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
        </p>
        <br /><br />
        <p class=MsoNormal>' . $this->trackingNumber . ' <span class=GramE>' . $this->packages[0]['packagingreferencenumbervalue'] . ' ' . round($this->codMonetaryValue, 2) . '</span> ' . $this->currencyCode . ' </p>
        <br /><br />
        <p class=MsoNormal>Total Number of Declared Value Packages = 1 </p>
        <p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
        </p>
        <br /><br />
        <p class=MsoNormal>RECEIVED BY_________________________PICKUP
        TIME__________________PKGS_______ </p>
        </div>
        </body>
        </html>';
                file_put_contents($path . "HVR" . $this->shipmentIdentificationNumber . ".html", $htmlHVReport);
            }
            return [
                'arrResponsXML' => $arrResponsXML,
                'digest' => '' . $this->shipmentDigest . '',
                'shipidnumber' => '' . $this->shipmentIdentificationNumber . '',
                'price' => ['currency' => $shiplabelcurrency, 'price' => $shiplabelprice],
            ];
        } else {
            $errorss = $xml->Response->Error[0];
            $this->_handy->_conf->log($errorss->ErrorDescription);
            return ['error' => $errorss->ErrorDescription, 'request' => $errorRequest, 'response' => $errorResponse];
        }
    }

    public function getShipPrice($type = 'shipment')
    {
        $this->_handy->_conf->createMediaFolders();
        $this->customerContext = $this->shipperName;
        $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'><AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber><UserId>" . $this->userId . "</UserId><Password>" . $this->password . "</Password></AccessRequest><?xml version=\"1.0\"?><RatingServiceSelectionRequest xml:lang=\"en-US\"><Request><TransactionReference><CustomerContext>" . $this->customerContext . "</CustomerContext><XpciVersion>1.0</XpciVersion></TransactionReference><RequestAction>Rate</RequestAction><RequestOption>Rate</RequestOption></Request>
  <Shipment><TaxInformationIndicator/>";
        if ($this->negotiatedRates == 1) {
            $data .= "
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= $this->setShipper() . $this->setShipTo($type) . $this->setShipFrom($type);

        if ($this->accesspoint == 1) {
            $data .= "<AlternateDeliveryAddress>
<Address>";
            if ($this->accesspointCity != '') {
                $data .= "<City>" . $this->accesspointCity . "</City>";
            }
            $data .= "<StateProvinceCode>" . $this->accesspointProvincecode . "</StateProvinceCode>
        <PostalCode>" . $this->accesspointPostal . "</PostalCode>
        <CountryCode>" . $this->accesspointCountry . "</CountryCode>";
            $data .= "</Address>
</AlternateDeliveryAddress>";
            $data .= "<ShipmentIndicationType><Code>" . $this->accesspointType . "</Code></ShipmentIndicationType>";
        }

        /*if ($this->shipmentcharge == 1) {
            $data .= "<ItemizedPaymentInformation>
            <ShipmentCharge>
      <Type>01</Type>
        <BillShipper>
          <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
        </BillShipper>
      </ShipmentCharge>
      <ShipmentCharge>
      <Type>02</Type>
        <BillShipper>
          <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
        </BillShipper>
      </ShipmentCharge>
    </ItemizedPaymentInformation>
    ";
        } else {
            $data .= "<PaymentInformation>
      <Prepaid>
        <BillShipper>
          <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
        </BillShipper>
      </Prepaid>
    </PaymentInformation>
    ";
        }*/
        $data .= "<Service>
      <Code>" . $this->serviceCode . "</Code>
      <Description>" . $this->serviceDescription . "</Description>
    </Service>";
        foreach ($this->packages as $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
                if (isset($pv['packagingreferencenumbercode2'])) {
                    $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode2'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue2'] . "</Value>
	  </ReferenceNumber>";
                }
            }
            $data .= array_key_exists('additionalhandling', $pv) && $pv['additionalhandling'] == 1 ? '<AdditionalHandling/>' : '';
            if ((isset($pv['length']) && isset($pv['width']) && isset($pv['height']) && strlen($pv['length']) > 0 && strlen($pv['width']) > 0 && strlen($pv['height']) > 0)
            ) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                $data .= "</UnitOfMeasurement>";
                $data .= "<Length>" . round($pv['length'], 0) . "</Length>
<Width>" . round($pv['width'], 0) . "</Width>
<Height>" . round($pv['height'], 0) . "</Height>";
                $data .= "</Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? $pv['weight'] : '';
            $weight = ceil($weight * 10) / 10;
            $weight = round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1);
            $data .= "</UnitOfMeasurement>
        <Weight>" . $weight . "</Weight>";
            $data .= $this->largePackageIndicator($pv);
            $data .= "</PackageWeight>
      <PackageServiceOptions>";
            $currencycode = $this->currencyCode;
            if (array_key_exists('insuredmonetaryvalue', $pv) && $pv['insuredmonetaryvalue'] > 0) {
                $insuredmonetaryvalue = array_key_exists('insuredmonetaryvalue', $pv) ? $pv['insuredmonetaryvalue'] : '';
                $data .= "<InsuredValue>
                <CurrencyCode>" . $currencycode . "</CurrencyCode>
                <MonetaryValue>" . $insuredmonetaryvalue . "</MonetaryValue>
                </InsuredValue>";
            }
            $cod = array_key_exists('cod', $pv) ? $pv['cod'] : 0;
            if ($cod == 1 && ($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR' || $this->shiptoCountryCode == 'CA') && ($this->shipfromCountryCode == 'US' || $this->shipfromCountryCode == 'PR' || $this->shipfromCountryCode == 'CA')) {
                $codfundscode = array_key_exists('codfundscode', $pv) ? $pv['codfundscode'] : '';
                $codmonetaryvalue = array_key_exists('codmonetaryvalue', $pv) ? $pv['codmonetaryvalue'] : '';
                $data .= "
              <COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $codfundscode . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $currencycode . "</CurrencyCod>
                      <MonetaryValue>" . $codmonetaryvalue . "</MonetaryValue>
                  </CODAmount>
              </COD>";
            }
            if ($this->isAdult('P')) {
                $data .= "<DeliveryConfirmation><DCISType>" . $this->adult . "</DCISType></DeliveryConfirmation>";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
        }
        $data .= "<ShipmentServiceOptions>";
        if ($this->codYesNo == 1 && $this->shiptoCountryCode != 'US' && $this->shiptoCountryCode != 'PR' && $this->shiptoCountryCode != 'CA' && $this->shipfromCountryCode != 'US' && $this->shipfromCountryCode != 'PR' && $this->shipfromCountryCode != 'CA') {
            $data .= "<COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $this->codFundsCode . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $this->currencyCode . "</CurrencyCod>
                      <MonetaryValue>" . $this->codMonetaryValue . "</MonetaryValue>
                  </CODAmount>
              </COD>";
        }
        if ($this->carbonNeutral == 1) {
            $data .= "<UPScarbonneutralIndicator/>";
        }
        if ($this->isAdult('S')) {
            $data .= "<DeliveryConfirmation><DCISType>" . $this->adult . "</DCISType></DeliveryConfirmation>";
        }
        $data .= "</ShipmentServiceOptions>";
        $data .= "</Shipment>
</RatingServiceSelectionRequest>
";
        
        file_put_contents($this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/RateRequest.xml', $data);

        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }

        $curl = $this->_handy->_conf;
        $curl->testing = !$this->testing;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/Rate', $data);
        if (!$curl->error) {
            file_put_contents($this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/RateResponse.xml', $result);
            $xml = simplexml_load_string($result);
            if (($xml->Response->ResponseStatusCode[0] == 1 || $xml->Response->ResponseStatusCode == 1) && isset($xml->RatedShipment)) {
                $ratedShipmentArray = $this->xml2array($xml);
                $price = null;
                if (!isset($ratedShipmentArray['RatedShipment'][0])) {
                    $ratedShipmentArray['RatedShipment'] = [$ratedShipmentArray['RatedShipment']];
                }
                foreach ($ratedShipmentArray['RatedShipment'] as $ratedShipment) {
                    if ($ratedShipment['Service']['Code'] == $this->serviceCode) {
                        $defaultPrice = $ratedShipment['TotalCharges']['MonetaryValue'];
                        $defaultCurrencyCode = $ratedShipment['TotalCharges']['CurrencyCode'];

                        if (isset($ratedShipment['TotalChargesWithTaxes'])) {
                            $totalChargesWithTaxes = $ratedShipment['TotalChargesWithTaxes'];
                            if (isset($totalChargesWithTaxes[0])) {
                                $totalChargesWithTaxes = $totalChargesWithTaxes[0];
                            }

                            if (isset($totalChargesWithTaxes) && isset($totalChargesWithTaxes['CurrencyCode'])
                                && isset($totalChargesWithTaxes['MonetaryValue'])
                            ) {
                                $defaultPrice = $totalChargesWithTaxes['MonetaryValue'];
                                $defaultCurrencyCode = $totalChargesWithTaxes['CurrencyCode'];
                            }
                        }

                        $priceNegotiatedRates = [];
                        if (isset($ratedShipment['NegotiatedRates'])) {
                            $priceNegotiatedRates['MonetaryValue'] = $ratedShipment['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'];
                            $priceNegotiatedRates['CurrencyCode'] = $ratedShipment['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['CurrencyCode'];

                            $defPrice = $ratedShipment['NegotiatedRates'];
                            if (isset($defPrice[0])) {
                                $defPrice = $defPrice[0];
                            }

                            $defPrice = $defPrice['NetSummaryCharges'];
                            if (isset($defPrice[0])) {
                                $defPrice = $defPrice[0];
                            }

                            if (isset($defPrice['GrandTotal'][0])) {
                                $priceNegotiatedRates['MonetaryValue'] = $defPrice['GrandTotal'][0]['MonetaryValue'];
                                $priceNegotiatedRates['CurrencyCode'] = $defPrice['GrandTotal'][0]['CurrencyCode'];
                            } else {
                                $priceNegotiatedRates['MonetaryValue'] = $defPrice['GrandTotal']['MonetaryValue'];
                                $priceNegotiatedRates['CurrencyCode'] = $defPrice['GrandTotal']['CurrencyCode'];
                            }


                            if (isset($defPrice['TotalChargesWithTaxes'])) {
                                $defPrice = $defPrice['TotalChargesWithTaxes'];
                                if (isset($defPrice[0])) {
                                    $defPrice = $defPrice[0];
                                }
                                $priceNegotiatedRates['MonetaryValue'] = $defPrice['MonetaryValue'];
                                $priceNegotiatedRates['CurrencyCode'] = $defPrice['CurrencyCode'];
                            }

                            if ($defaultPrice < $priceNegotiatedRates['MonetaryValue']) {
                                $defaultPrice = "no price";
                                $defaultCurrencyCode = "";
                            }
                        }

                        $price = [
                            'def' => ['MonetaryValue' => $defaultPrice, 'CurrencyCode' => $defaultCurrencyCode],
                            'negotiated' => $priceNegotiatedRates
                        ];

                        break;
                    }
                }
                return json_encode([
                    'price' => $price,
                    'methods' => $ratedShipmentArray['RatedShipment'],
                ]);
            } else {
                $error = ['error' => $xml->Response[0]->Error[0]->ErrorDescription[0]];
                return json_encode($error);
            }
        } else {
            return $result;
        }
    }

    public function getShipPriceFrom($type = 'refund')
    {
        $this->_handy->_conf->createMediaFolders();
        $this->customerContext = $this->shipperName;
        $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'><AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber><UserId>" . $this->userId . "</UserId><Password>" . $this->password . "</Password></AccessRequest><?xml version=\"1.0\"?><RatingServiceSelectionRequest xml:lang=\"en-US\"><Request><TransactionReference><CustomerContext>" . $this->customerContext . "</CustomerContext><XpciVersion/></TransactionReference><RequestAction>Rate</RequestAction><RequestOption>Rate</RequestOption></Request>
          <Shipment>";
        if ($this->negotiatedRates == 1) {
            $data .= "<RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= $this->setShipper() . $this->setShipTo($type) . $this->setShipFrom($type) . "
            <Service>
              <Code>" . $this->serviceCode . "</Code>
              <Description>" . $this->serviceDescription . "</Description>
            </Service>";
        foreach ($this->packages as $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
                if (isset($pv['packagingreferencenumbercode2'])) {
                    $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode2'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue2'] . "</Value>
	  </ReferenceNumber>";
                }
            }
            $data .= array_key_exists('additionalhandling', $pv) && $pv['additionalhandling'] == 1 ? '<AdditionalHandling/>' : '';
            if ((isset($pv['length']) && isset($pv['width']) && isset($pv['height']) && strlen($pv['length']) > 0 && strlen($pv['width']) > 0 && strlen($pv['height']) > 0)
            ) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                $data .= "</UnitOfMeasurement>";
                $data .= "<Length>" . round($pv['length'], 0) . "</Length>
<Width>" . round($pv['width'], 0) . "</Width>
<Height>" . round($pv['height'], 0) . "</Height>";
                $data .= "</Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            $weight = array_key_exists('weight', $pv) ? (float)str_replace(',', '.', $pv['weight']) : '';
            $weight = ceil($weight * 10) / 10;
            $weight = round(($weight + (is_numeric(str_replace(',', '.', $pv['packweight'])) ? $pv['packweight'] : 0)), 1);
            $data .= "</UnitOfMeasurement>
        <Weight>" . $weight . "</Weight>";
            $data .= $this->largePackageIndicator($pv);
            $data .= "</PackageWeight>
      <PackageServiceOptions>";
            if ($pv['insuredmonetaryvalue'] > 0) {
                $data .= "<InsuredValue>
                <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                <MonetaryValue>" . (isset($pv['insuredmonetaryvalue']) ? $pv['insuredmonetaryvalue'] : 0) . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            if (isset($pv['cod']) && $pv['cod'] == 1 && ($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR' || $this->shiptoCountryCode == 'CA') && ($this->shipfromCountryCode == 'US' || $this->shipfromCountryCode == 'PR' || $this->shipfromCountryCode == 'CA')) {
                $data .= "
              <COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>0</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $this->currencyCode . "</CurrencyCod>
                      <MonetaryValue>" . (isset($pv['codmonetaryvalue']) ? $pv['codmonetaryvalue'] : 0) . "</MonetaryValue>
                  </CODAmount>
              </COD>";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
            break;
        }
        $data .= "</Shipment>
        </RatingServiceSelectionRequest>
        ";

        file_put_contents($this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/RateReturnRequest.xml', $data);

        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }

        $curl = $this->_handy->_conf;
        $curl->testing = !$this->testing;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/Rate', $data);

        if (!$curl->error) {
            file_put_contents($this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/RateReturnResponse.xml', $result);
            $xml = simplexml_load_string($result);
            if (($xml->Response->ResponseStatusCode[0] == 1 || $xml->Response->ResponseStatusCode == 1) && isset($xml->RatedShipment)) {
                $ratedShipmentArray = $this->xml2array($xml);
                $price = null;
                if (!isset($ratedShipmentArray['RatedShipment'][0])) {
                    $ratedShipmentArray['RatedShipment'] = [$ratedShipmentArray['RatedShipment']];
                }
                foreach ($ratedShipmentArray['RatedShipment'] as $ratedShipment) {
                    if ($ratedShipment['Service']['Code'] == $this->serviceCode) {
                        $defaultPrice = $ratedShipment['TotalCharges']['MonetaryValue'];
                        $defaultCurrencyCode = $ratedShipment['TotalCharges']['CurrencyCode'];

                        if (isset($ratedShipment['TotalChargesWithTaxes'])) {
                            $totalChargesWithTaxes = $ratedShipment['TotalChargesWithTaxes'];
                            if (isset($totalChargesWithTaxes[0])) {
                                $totalChargesWithTaxes = $totalChargesWithTaxes[0];
                            }

                            if (isset($totalChargesWithTaxes) && isset($totalChargesWithTaxes['CurrencyCode'])
                                && isset($totalChargesWithTaxes['MonetaryValue'])
                            ) {
                                $defaultPrice = $totalChargesWithTaxes['MonetaryValue'];
                                $defaultCurrencyCode = $totalChargesWithTaxes['CurrencyCode'];
                            }
                        }

                        $priceNegotiatedRates = [];
                        if (isset($ratedShipment['NegotiatedRates'])) {
                            $priceNegotiatedRates['MonetaryValue'] = $ratedShipment['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['MonetaryValue'];
                            $priceNegotiatedRates['CurrencyCode'] = $ratedShipment['NegotiatedRates']['NetSummaryCharges']['GrandTotal']['CurrencyCode'];

                            $defPrice = $ratedShipment['NegotiatedRates'];
                            if (isset($defPrice[0])) {
                                $defPrice = $defPrice[0];
                            }

                            $defPrice = $defPrice['NetSummaryCharges'];
                            if (isset($defPrice[0])) {
                                $defPrice = $defPrice[0];
                            }

                            if (isset($defPrice['GrandTotal'][0])) {
                                $priceNegotiatedRates['MonetaryValue'] = $defPrice['GrandTotal'][0]['MonetaryValue'];
                                $priceNegotiatedRates['CurrencyCode'] = $defPrice['GrandTotal'][0]['CurrencyCode'];
                            } else {
                                $priceNegotiatedRates['MonetaryValue'] = $defPrice['GrandTotal']['MonetaryValue'];
                                $priceNegotiatedRates['CurrencyCode'] = $defPrice['GrandTotal']['CurrencyCode'];
                            }


                            if (isset($defPrice['TotalChargesWithTaxes'])) {
                                $defPrice = $defPrice['TotalChargesWithTaxes'];
                                if (isset($defPrice[0])) {
                                    $defPrice = $defPrice[0];
                                }
                                $priceNegotiatedRates['MonetaryValue'] = $defPrice['MonetaryValue'];
                                $priceNegotiatedRates['CurrencyCode'] = $defPrice['CurrencyCode'];
                            }

                            if ($defaultPrice < $priceNegotiatedRates['MonetaryValue']) {
                                $defaultPrice = "no price";
                                $defaultCurrencyCode = "";
                            }
                        }

                        $price = [
                            'def' => ['MonetaryValue' => $defaultPrice, 'CurrencyCode' => $defaultCurrencyCode],
                            'negotiated' => $priceNegotiatedRates
                        ];

                        break;
                    }
                }
                return json_encode([
                    'price' => $price,
                    'methods' => $ratedShipmentArray['RatedShipment'],
                ]);
            } else {
                $error = ['error' => $xml->Response[0]->Error[0]->ErrorDescription[0]];
                return json_encode($error);
            }
        } else {
            return $result;
        }
    }

    public function deleteLabel($trnum)
    {
        $this->_handy->_conf->createMediaFolders();
        $pathXml = $this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/';
        $cie = 'wwwcie';
        $testing = $this->testing;
        $shipIndefNumbr = $trnum;
        if (0 == $testing) {
            $cie = 'onlinetools';
        } else {
            /*$trnum = '1Z2220060291994175';*/
            $shipIndefNumbr = '1ZISDE016691676846';
        }
        $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'><AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber><UserId>" . $this->userId . "</UserId><Password>" . $this->password . "</Password></AccessRequest><?xml version=\"1.0\" ?><VoidShipmentRequest><Request><RequestAction>1</RequestAction></Request><ShipmentIdentificationNumber>" . $shipIndefNumbr . "</ShipmentIdentificationNumber><ExpandedVoidShipment><ShipmentIdentificationNumber>" . $shipIndefNumbr . "</ShipmentIdentificationNumber></ExpandedVoidShipment></VoidShipmentRequest>";
        file_put_contents($pathXml . "VoidShipmentRequest.xml", $data);

        $curl = $this->_handy->_conf;
        $curl->testing = !$this->testing;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/Void', $data);
        if (!$curl->error) {
            file_put_contents($pathXml . "VoidShipmentResponse.xml", $result);
            $xml = simplexml_load_string($result);
            if ($xml->Response->Error[0] && (int)$xml->Response->Error[0]->ErrorCode != 190117) {
                $errorss = $xml->Response->Error[0];
                return ['error' => $errorss->ErrorDescription];
            } else {
                return null;
            }
        } else {
            return $result;
        }
    }

    public function getPickup()
    {
        $this->_handy->_conf->createMediaFolders();
        $pathXml = $this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/';
        $this->customerContext = str_replace('&', '&amp;', strtolower($this->shipfromCompanyName));

        $data = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:wsf="http://www.ups.com/schema/wsf" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">
	<envr:Header>
		<upss:UPSSecurity>
			<upss:UsernameToken>
				<upss:Username>' . $this->userId . '</upss:Username>
				<upss:Password>' . $this->password . '</upss:Password>
			</upss:UsernameToken>
			<upss:ServiceAccessToken>
				<upss:AccessLicenseNumber>' . $this->accessLicenseNumber . '</upss:AccessLicenseNumber>
			</upss:ServiceAccessToken>
		</upss:UPSSecurity>
		<common:ClientInformation>
			<common:Property Key="DataSource">AG</common:Property>
			<common:Property Key="ClientCode">APS</common:Property>
		</common:ClientInformation>
	</envr:Header>';
        $data .= "<envr:Body><PickupCreationRequest xmlns=\"http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1\" xmlns:common=\"http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
    <RatePickupIndicator>" . $this->ratePickupIndicator . "</RatePickupIndicator>
    <Shipper>
        <Account>
            <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
            <AccountCountryCode>" . $this->shipperCountryCode . "</AccountCountryCode>
        </Account>
    </Shipper>
    <PickupDateInfo>
        <CloseTime>" . str_replace(",", "", substr($this->closeTime, 0, 5)) . "</CloseTime>
        <ReadyTime>" . str_replace(",", "", substr($this->readyTime, 0, 5)) . "</ReadyTime>
        <PickupDate>" . ($this->pickupDateYear . $this->pickupDateMonth . $this->pickupDateDay) . "</PickupDate>
    </PickupDateInfo>
    <PickupAddress>
        <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
        <ContactName>" . $this->shipfromAttentionName . "</ContactName>
        <AddressLine>" . $this->shipfromAddressLine1 . "</AddressLine>";
        if (strlen($this->room) > 0) {
            $data .= "<Room>" . $this->room . "</Room>";
        }
        if (strlen($this->floor) > 0) {
            $data .= "<Floor>" . $this->floor . "</Floor>";
        }
        $data .= "<City>" . $this->shipfromCity . "</City>";
        if (strlen($this->shipfromStateProvinceCode) > 0) {
            $data .= "<StateProvince>" . $this->shipfromStateProvinceCode . "</StateProvince>";
        }
        if (strlen($this->urbanization) > 0) {
            $data .= "<Urbanization>" . $this->urbanization . "</Urbanization>";
        }
        $data .= "<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
        <CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
        <ResidentialIndicator>" . $this->residential . "</ResidentialIndicator>";
        if (strlen($this->pickupPoint) > 0) {
            $data .= "<PickupPoint>" . $this->pickupPoint . "</PickupPoint>";
        }
        $data .= "<Phone><Number>" . $this->shipfromPhoneNumber . "</Number></Phone>
    </PickupAddress>
    <AlternateAddressIndicator>" . $this->alternateAddressIndicator . "</AlternateAddressIndicator>
    <PickupPiece>
        <ServiceCode>" . $this->serviceCode . "</ServiceCode>
        <Quantity>" . $this->quantity . "</Quantity>
        <DestinationCountryCode>" . $this->destinationCountryCode . "</DestinationCountryCode>
        <ContainerCode>" . $this->containerCode . "</ContainerCode>
    </PickupPiece>";
        if (strlen($this->weight) > 0) {
            $data .= "<TotalWeight>
            <Weight>" . $this->weight . "</Weight>
            <UnitOfMeasurement>" . $this->unitOfMeasurement . "</UnitOfMeasurement>
            <OverweightIndicator>" . $this->overweightIndicator . "</OverweightIndicator>
        </TotalWeight>";
        }
        $data .= "
    <PaymentMethod>" . $this->paymentMethod . "</PaymentMethod>
    ";
        if (strlen($this->specialInstruction) > 0) {
            $data .= "<SpecialInstruction>" . $this->specialInstruction . "</SpecialInstruction>";
        }
        if (strlen($this->referenceNumber) > 0) {
            $data .= "<ReferenceNumber>" . $this->referenceNumber . "</ReferenceNumber>";
        }
        if ($this->notification == 1) {
            $data .= "<Notification>";
            $confirmEmail = explode(",", $this->confirmationEmailAddress);
            if (count($confirmEmail) > 0) {
                foreach ($confirmEmail as $v) {
                    $data .= "<ConfirmationEmailAddress>" . trim($v) . "</ConfirmationEmailAddress>";
                }
            }
            $data .= "<UndeliverableEmailAddress>" . $this->undeliverableEmailAddress . "</UndeliverableEmailAddress>";
            $data .= "</Notification>";
        }
        $data .= "
</PickupCreationRequest></envr:Body>
</envr:Envelope>";
        file_put_contents($pathXml . "PickupRequest.xml", $data);
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $curl = $this->_handy->_conf;
        $result = $curl->curlSetOption('https://' . $cie . '.ups.com/webservices/Pickup', $data);
        $result = strstr($result, '<soapenv:');
        if ($result) {
            file_put_contents($pathXml . "PickupResponse.xml", $result);
        }
        //return $result;
        $xml = simplexml_load_string($result);
        $soap = $xml->children('soapenv', true)->Body[0];
        $response = $soap->children('pkup', true);
        $common = $response->children('common', true);
        if ($response && $common && (int)$common->Response[0]->ResponseStatus[0]->Code[0] == 1 && (string)$common->Response[0]->ResponseStatus[0]->Description[0] == "Success") {
            return [
                'Description' => $response->children('common', true)->Response[0]->ResponseStatus[0]->Description[0],
                'data' => $data,
                'response' => $result
            ];
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $soap->Fault[0]->children()->detail[0]->children('err', true)->Errors[0]->ErrorDetail[0];
            $error .= '<li>Error Severity : ' . $errorss->Severity[0] . '</li>';
            $error .= '<li>Error Code : ' . $errorss->PrimaryErrorCode[0]->Code[0] . '</li>';
            $error .= '<li>Error Description : ' . $errorss->PrimaryErrorCode[0]->Description[0] . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return ['error' => $error, 'data' => $data, 'response' => $result];
            //return print_r($xml->Response->Error);
        }
    }

    public function cancelPickup($PRN)
    {
        $this->_handy->_conf->createMediaFolders();
        $pathXml = $this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/';
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
            /*$PRN = '02';*/
        }
        /*else {
            $PRN = '2929602E9CP';
        }*/
        $data = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:wsf="http://www.ups.com/schema/wsf" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">
	<envr:Header>
		<upss:UPSSecurity>
			<upss:UsernameToken>
				<upss:Username>' . $this->userId . '</upss:Username>
				<upss:Password>' . $this->password . '</upss:Password>
			</upss:UsernameToken>
			<upss:ServiceAccessToken>
				<upss:AccessLicenseNumber>' . $this->accessLicenseNumber . '</upss:AccessLicenseNumber>
			</upss:ServiceAccessToken>
		</upss:UPSSecurity>
		<common:ClientInformation>
			<common:Property Key="DataSource">AG</common:Property>
			<common:Property Key="ClientCode">APS</common:Property>
		</common:ClientInformation>
	</envr:Header>';
        $data .= "<envr:Body><PickupCancelRequest xmlns=\"http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1\" xmlns:common=\"http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
        <Request></Request>
        <CancelBy>02</CancelBy>
        <PRN>" . $PRN . "</PRN>";
        $data .= "</PickupCancelRequest></envr:Body>
</envr:Envelope>";
        file_put_contents($pathXml . "PickupCancelRequest.xml", $data);

        $curl = $this->_handy->_conf;
        $result = $curl->curlSetOption('https://' . $cie . '.ups.com/webservices/Pickup', $data);
        $result = strstr($result, '<soapenv:');
        if ($result) {
            file_put_contents($pathXml . "PickupCancelResponse.xml", $result);
        }

        $xml = simplexml_load_string($result);
        $soap = $xml->children('soapenv', true)->Body[0];
        $response = $soap->children('pkup', true);
        $common = $response->children('common', true);
        if ($response && $common && (int)$common->Response[0]->ResponseStatus[0]->Code[0] == 1 && (string)$common->Response[0]->ResponseStatus[0]->Description[0] == "Success") {
            return [
                'Description' => "Canceled",
                'data' => $data,
                'response' => $result
            ];
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $soap->Fault[0]->children()->detail[0]->children('err', true)->Errors[0]->ErrorDetail[0];
            $error .= '<li>Error Severity : ' . $errorss->Severity[0] . '</li>';
            $error .= '<li>Error Code : ' . $errorss->PrimaryErrorCode[0]->Code[0] . '</li>';
            $error .= '<li>Error Description : ' . $errorss->PrimaryErrorCode[0]->Description[0] . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return ['error' => $error, 'data' => $data, 'response' => $result];
            //return print_r($xml->Response->Error);
        }
    }

    public function statusPickup()
    {
        /* if(is_dir($filename)){} */
        $this->_handy->_conf->createMediaFolders();
        $pathXml = $this->_handy->_conf->getBaseDir('media') . '/upslabel/test_xml/';
        $data = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:wsf="http://www.ups.com/schema/wsf" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">
	<envr:Header>
		<upss:UPSSecurity>
			<upss:UsernameToken>
				<upss:Username>' . $this->userId . '</upss:Username>
				<upss:Password>' . $this->password . '</upss:Password>
			</upss:UsernameToken>
			<upss:ServiceAccessToken>
				<upss:AccessLicenseNumber>' . $this->accessLicenseNumber . '</upss:AccessLicenseNumber>
			</upss:ServiceAccessToken>
		</upss:UPSSecurity>
		<common:ClientInformation>
			<common:Property Key="DataSource">AG</common:Property>
			<common:Property Key="ClientCode">APS</common:Property>
		</common:ClientInformation>
	</envr:Header>';
        $data .= "<envr:Body><PickupPendingStatusRequest xmlns=\"http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1\" xmlns:common=\"http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
        <Request></Request>
        <PickupType>01</PickupType>
        <AccountNumber>" . $this->shipperNumber . "</AccountNumber>";
        $data .= "</PickupPendingStatusRequest></envr:Body>
</envr:Envelope>";
        file_put_contents($pathXml . "PickupPendingStatusRequest.xml", $data);
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }

        $curl = $this->_handy->_conf;
        $result = $curl->curlSetOption('https://' . $cie . '.ups.com/webservices/Pickup', $data);
        $result = strstr($result, '<soapenv:');
        if ($result) {
            file_put_contents($pathXml . "PickupPendingStatusResponse.xml", $result);
        }
        $xml = simplexml_load_string($result);
        $soap = $xml->children('soapenv', true)->Body[0];
        $response = $soap->children('pkup', true);
        if ($response && $response->children('common', true)->Response[0]->ResponseStatus[0]->Code[0] == 1 && $response->children('common', true)->Response[0]->ResponseStatus[0]->Description[0] == "Success") {
            return [
                'Description' => "Canceled",
                'data' => $data,
                'response' => $result
            ];
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $soap->Fault[0]->children()->detail[0]->children('err', true)->Errors[0]->ErrorDetail[0];
            $error .= '<li>Error Severity : ' . $errorss->Severity[0] . '</li>';
            $error .= '<li>Error Code : ' . $errorss->PrimaryErrorCode[0]->Code[0] . '</li>';
            $error .= '<li>Error Description : ' . $errorss->PrimaryErrorCode[0]->Description[0] . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return ['error' => $error, 'data' => $data, 'response' => $result];
            //return print_r($xml->Response->Error);
        }
    }

    public function ratePickup()
    {
        $this->_handy->_conf->createMediaFolders();
        $data = '<envr:Envelope xmlns:envr="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:common="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:wsf="http://www.ups.com/schema/wsf" xmlns:upss="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0">
	<envr:Header>
		<upss:UPSSecurity>
			<upss:UsernameToken>
				<upss:Username>' . $this->userId . '</upss:Username>
				<upss:Password>' . $this->password . '</upss:Password>
			</upss:UsernameToken>
			<upss:ServiceAccessToken>
				<upss:AccessLicenseNumber>' . $this->accessLicenseNumber . '</upss:AccessLicenseNumber>
			</upss:ServiceAccessToken>
		</upss:UPSSecurity>
		<common:ClientInformation>
			<common:Property Key="DataSource">AG</common:Property>
			<common:Property Key="ClientCode">APS</common:Property>
		</common:ClientInformation>
	</envr:Header>';
        $data .= "<envr:Body><PickupRateRequest xmlns=\"http://www.ups.com/XMLSchema/XOLTWS/Pickup/v1.1\" xmlns:common=\"http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
        <Request></Request>
    <PickupAddress>
        <AddressLine>" . $this->shipfromAddressLine1 . "</AddressLine>";
        $data .= "<City>" . $this->shipfromCity . "</City>";
        $data .= "<StateProvince>" . $this->shipfromStateProvinceCode . "</StateProvince>";
        $data .= "<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
        <CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
        <ResidentialIndicator>" . $this->residential . "</ResidentialIndicator>";
        $data .= "</PickupAddress>
    <AlternateAddressIndicator>" . $this->alternateAddressIndicator . "</AlternateAddressIndicator>
    <ServiceDateOption>" . ($this->pickupDateYear . $this->pickupDateMonth . $this->pickupDateDay == date("Ymd") ? "01" : "02") . "</ServiceDateOption>
    <PickupDateInfo>
        <CloseTime>" . str_replace(",", "", substr($this->closeTime, 0, 5)) . "</CloseTime>
        <ReadyTime>" . str_replace(",", "", substr($this->readyTime, 0, 5)) . "</ReadyTime>
        <PickupDate>" . ($this->pickupDateYear . $this->pickupDateMonth . $this->pickupDateDay) . "</PickupDate>
    </PickupDateInfo>";
        $data .= "</PickupRateRequest></envr:Body>
</envr:Envelope>";
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $curl = $this->_handy->_conf;
        $result = $curl->curlSetOption('https://' . $cie . '.ups.com/webservices/Pickup', $data);
        $result = strstr($result, '<soapenv:');
        $xml = simplexml_load_string($result);
        $soap = $xml->children('soapenv', true);
        if ($soap->count() > 0) {
            $soap = $soap->Body[0];
            $response = $soap->children('pkup', true);
            if ($response->count() > 0) {
                $common = $response->children('common', true);
                if ($common->count() > 0 && $common->Response[0]->ResponseStatus[0]->Code[0] == 1 && $common->Response[0]->ResponseStatus[0]->Description[0] == "Success") {
                    $pukp = $soap->children('pkup', true);
                    if ($pukp->count() > 0) {
                        return $pukp->PickupRateResponse[0]->RateResult[0]->GrandTotalOfAllCharge;
                    }
                }
            }
        }
        return 0;
    }

    function getShipRate()
    {
        $this->_handy->_conf->createMediaFolders();
        $this->customerContext = $this->shipperName;
        $weightSum = 0;
        $data = "<?xml version=\"1.0\" ?><AccessRequest xml:lang='en-US'><accessLicenseNumber>" . $this->accessLicenseNumber . "</accessLicenseNumber><UserId>" . $this->userId . "</UserId><Password>" . $this->password . "</Password></AccessRequest>
<?xml version=\"1.0\"?>
<RatingServiceSelectionRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
      <CustomerContext>Rating and Service</CustomerContext>
      <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
    <RequestAction>Rate</RequestAction>
    <RequestOption>Shop</RequestOption>
  </Request>
  <PickupType>
          <Code>03</Code>
          <Description>Customer Counter</Description>
  </PickupType>
  <Shipment>";
        if ($this->negotiatedRates == 1) {
            $data .= "
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        $data .= "<Shipper>";
        $data .= "<ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
      <Address>
    	<City>" . $this->shipperCity . "</City>
    	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
     </Address>
    </Shipper>
	<ShipTo>
      <Address>
        <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
        <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
        <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
        <ResidentialAddress>02</ResidentialAddress>
      </Address>
    </ShipTo>
    <ShipFrom>
      <Address>
    	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
      </Address>
    </ShipFrom>";
        if ($this->accesspoint == 1) {
            $data .= "<AlternateDeliveryAddress>
<Address>";
            if ($this->accesspointCity != '') {
                $data .= "<City>" . $this->accesspointCity . "</City>";
            }
            $data .= "<StateProvinceCode>" . $this->accesspointProvincecode . "</StateProvinceCode>
        <PostalCode>" . $this->accesspointPostal . "</PostalCode>
        <CountryCode>" . $this->accesspointCountry . "</CountryCode>";
            $data .= "</Address>
</AlternateDeliveryAddress>";
            $data .= "<ShipmentIndicationType><Code>" . $this->accesspointType . "</Code></ShipmentIndicationType>";
        }
        foreach ($this->packages as $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>";
            $data .= array_key_exists('additionalhandling', $pv) && $pv['additionalhandling'] == 1 ? '<AdditionalHandling/>' : '';
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? (float)str_replace(',', '.', $pv['weight']) : '';
            $weight = ceil($weight * 10) / 10;
            $weight = round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1);
            $weightSum += $weight;
            $data .= "</UnitOfMeasurement>
        <Weight>" . $weight . "</Weight>";
            $data .= $this->largePackageIndicator($pv);
            $data .= "</PackageWeight>
              </Package>";
        }
        $data .= "</Shipment></RatingServiceSelectionRequest>";
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $curl = $this->_handy->_conf;
        $result = $curl->curlSetOption('https://' . $cie . '.ups.com/ups.app/xml/Rate', $data);
        $result = strstr($result, '<?xml');
        //return $data;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $rates = [];
            $timeInTransit = null;
            foreach ($xml->RatedShipment as $rated) {
                if (is_array($rated->Service) && count($rated->Service) > 0) {
                    if (is_array($rated->Service[0]->Code) && count($rated->Service[0]->Code) > 0) {
                        $rateCode = (string)$rated->Service[0]->Code[0];
                    } else {
                        $rateCode = (string)$rated->Service[0]->Code;
                    }
                } else {
                    if (is_array($rated->Service->Code) && count($rated->Service->Code) > 0) {
                        $rateCode = (string)$rated->Service->Code[0];
                    } else {
                        $rateCode = (string)$rated->Service->Code;
                    }
                }
                $time = (string)$rated->GuaranteedDaysToDelivery;
                /*if ($rated->Service[0]->Code[0] == $this->serviceCode) {*/
                $defaultPrice = $rated->TotalCharges[0]->MonetaryValue;
                if (!$rated->NegotiatedRates) {
                    $rates[$rateCode] = [
                        'price' => $defaultPrice,
                        'time' => $time
                    ];
                } else {
                    $defaultPrice = $rated->NegotiatedRates[0]->NetSummaryCharges[0]->GrandTotal[0]->MonetaryValue;
                    if ($this->ratesTax == 1) {
                        $defaultPrice2 = $rated->NegotiatedRates[0]->NetSummaryCharges[0]->TotalChargesWithTaxes[0]->MonetaryValue;
                        if ($defaultPrice2) {
                            $defaultPrice = $defaultPrice2;
                        }
                    }
                    $rates[$rateCode] = [
                        'price' => $defaultPrice,
                        'time' => $time
                    ];
                }
                /*}*/
                if ($timeInTransit === null) {
                    $timeInTransit = $this->timeInTransit($weightSum);
                }
                if (is_array($timeInTransit) && isset($timeInTransit['days'][$rateCode])) {
                    $rates[$rateCode]['day'] = $timeInTransit['days'][$rateCode];
                }
            }
            return $rates;
        } else {
            $error = ['error' => $xml->Response[0]->Error[0]->ErrorDescription[0]];
            return $error;
        }
    }

    function setShipper()
    {
        return "<Shipper>
    <Name>" . $this->shipperName . "</Name>
    <AttentionName>" . $this->shipperAttentionName . "</AttentionName>
    <PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
      <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
    	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
    	<City>" . $this->shipperCity . "</City>
    	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
    	<PostcodeExtendedLow></PostcodeExtendedLow>
    	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
     </Address>
    </Shipper>";
    }

    function setShipFrom($type = 'shipment')
    {
        if ($type == 'shipment') {
            return "<ShipFrom>
      <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
      <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
        <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>
        <City>" . $this->shipfromCity . "</City>
    	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
      </Address>
    </ShipFrom>";
        } else {
            $data = "<ShipFrom>
             <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
              <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>";
            if (strlen($this->shiptoPhoneNumber) > 0) {
                $data .= "<PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>";
            } elseif ($this->serviceCode == 14 || $this->shiptoCountryCode != $this->shipfromCountryCode) {
                $data .= "<PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>";
            }

            $data .= "<TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
                <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>
                <City>" . $this->shiptoCity . "</City>
                <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
                <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
                <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
              </Address>
            </ShipFrom>";
            return $data;
        }
    }

    function setShipTo($type = 'shipment')
    {
        if ($type == 'shipment') {
            $data = "<ShipTo>
     <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
      <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>";
            if (strlen($this->shiptoPhoneNumber) > 0) {
                $data .= "<PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>";
            } elseif ($this->serviceCode == 14 || $this->shiptoCountryCode != $this->shipfromCountryCode) {
                $data .= "<PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>";
            }
            $data .= "<Address><AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>";
            if (strlen($this->shiptoAddressLine2) > 0) {
                $data .= '<AddressLine2>' . $this->shiptoAddressLine2 . '</AddressLine2>';
            }
            $data .= "<City>" . $this->shiptoCity . "</City>";
            if (strlen($this->shiptoStateProvinceCode) > 0) {
                $data .= "<StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>";
            } else {
                $data .= "<StateProvinceCode/>";
            }

            $data .= "<PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
        <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>";
            if ($this->residentialAddress == 1) {
                $data .= "<ResidentialAddress />";
            }
            $data .= "
      </Address>
    </ShipTo>";
            return $data;
        } else {
            $data = "<ShipTo>
              <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
              <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
              <Address>
                <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>";
            if (strlen($this->shipfromAddressLine2) > 0) {
                $data .= '<AddressLine2>' . $this->shipfromAddressLine2 . '</AddressLine2>';
            }

            $data .= "
                <City>" . $this->shipfromCity . "</City>";
            if (strlen($this->shipfromStateProvinceCode) > 0) {
                $data .= "<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>";
            } else {
                $data .= "<StateProvinceCode/>";
            }
            $data .= "<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
            	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
              </Address>
            </ShipTo>";
            return $data;
        }
    }

    public function timeInTransit($weightSum)
    {
        $this->_handy->_conf->createMediaFolders();
        $cie = 'wwwcie';
        $testing = $this->testing;
        if (0 == $testing) {
            $cie = 'onlinetools';
        }
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->accessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->userId . "</UserId>
<Password>" . $this->password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<TimeInTransitRequest xml:lang='en-US'>
<Request>
<TransactionReference>
<CustomerContext>Shipper</CustomerContext>
<XpciVersion>1.0002</XpciVersion>
</TransactionReference>
<RequestAction>TimeInTransit</RequestAction>
</Request>
<TransitFrom>
<AddressArtifactFormat>
<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
<PostcodePrimaryLow>" . $this->shipfromPostalCode . "</PostcodePrimaryLow>
</AddressArtifactFormat>
</TransitFrom>
<TransitTo>
<AddressArtifactFormat>
<PoliticalDivision2>" . $this->shiptoCity . "</PoliticalDivision2>
<PoliticalDivision1>" . $this->shiptoStateProvinceCode . "</PoliticalDivision1>
<CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
<PostcodePrimaryLow>" . $this->shiptoPostalCode . "</PostcodePrimaryLow>
</AddressArtifactFormat>
</TransitTo>
<ShipmentWeight>
<UnitOfMeasurement>
<Code>" . $this->weightUnits . "</Code>
</UnitOfMeasurement>
<Weight>" . $weightSum . "</Weight>
</ShipmentWeight>
<PickupDate>" . date('Ymd') . "</PickupDate>
<DocumentsOnlyIndicator />
</TimeInTransitRequest>";
        $curl = $this->_handy->_conf;
        $curl->testing = !$this->testing;
        $result = $curl->curlSend('https://' . $cie . '.ups.com/ups.app/xml/TimeInTransit', $data);
        if (!$curl->error) {
            $xml = $this->xml2array(simplexml_load_string($result));
            if ($xml['Response']['ResponseStatusCode'] == 0 || $xml['Response']['ResponseStatusDescription'] != 'Success') {
                return ['error' => 1];
            } else {
                $countDay = [];
                if (isset($xml['TransitResponse'])) {
                    foreach ($xml['TransitResponse']['ServiceSummary'] as $v) {
                        if (isset($v['EstimatedArrival']['TotalTransitDays'])) {
                            $countDay[$this->_handy->_conf->getUpsCode($v['Service']['Code'])] = $v['EstimatedArrival']['TotalTransitDays'];
                        } elseif (isset($v['EstimatedArrival']['BusinessTransitDays'])) {
                            $countDay[$this->_handy->_conf->getUpsCode($v['Service']['Code'])] = $v['EstimatedArrival']['BusinessTransitDays'];
                        }
                    }
                }
                return ['error' => 0, 'days' => $countDay];
            }
        } else {
            return $result;
        }
    }

    public function xml2array($xmlObject)
    {
        $outString = json_encode($xmlObject);
        unset($xmlObject);
        return json_decode($outString, true);
    }

    protected function isAdult($typeService)
    {
        if ($this->adult == 4) {
            if ($typeService === "P") {
                return false;
            } elseif ($typeService === "S") {
                return true;
            }
        }

        if ($typeService === "S") {
            $this->adult = $this->adult - 1;
        }

        if ($this->adult <= 0) {
            return false;
        }

        $adult = 'DC';
        if ($typeService === 'P') {
            if ($this->adult == 2) {
                $adult = 'DC-SR';
            } elseif ($this->adult == 3) {
                $adult = 'DC-ASR';
            }
        } elseif ($typeService === 'S') {
            if ($this->adult == 1) {
                $adult = 'DC-SR';
            } elseif ($this->adult == 2) {
                $adult = 'DC-ASR';
            }
        }

        switch ($this->shipfromCountryCode) {
            case 'US':
            case 'CA':
            case 'PR':
                switch ($this->shiptoCountryCode) {
                    case 'US':
                    case 'PR':
                        if ($typeService === 'P') {
                            return true;
                        }
                        break;
                    default:
                        if ($typeService === 'S' && ($adult === 'DC-SR' || $adult === 'DC-ASR')) {
                            return true;
                        }
                        break;
                }
                break;
            default:
                if ($typeService === 'S' && ($adult === 'DC-SR' || $adult === 'DC-ASR')) {
                    return true;
                }
                break;
        }

        return false;
    }

    private function largePackageIndicator($pv)
    {
        if (isset($pv['weight']) && $pv['weight'] > 0 && isset($pv['height']) && $pv['height'] > 0 && isset($pv['length']) && $pv['length'] > 0) {
            $maxDimension = 130;
            if ($this->unitOfMeasurement == 'CM') {
                $maxDimension = 330;
            }

            if (($pv['weight'] * 2 + $pv['height'] * 2) >= $maxDimension) {
                return '<LargePackageIndicator />';
            }
        }
        return '';
    }
}