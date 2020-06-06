<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */

namespace Infomodus\Upslabel\Helper;

use DVDoug\BoxPacker\ItemTooLargeException;
use DVDoug\BoxPacker\Packer;
use Infomodus\Upslabel\Model\Packer\TestBox as PackerBox;
use Infomodus\Upslabel\Model\Packer\TestItem as PackerItem;
use Magento\Framework\App\Helper\AbstractHelper;

class Handy extends AbstractHelper
{
    public $_context;
    public $objectManager;
    public $_conf;
    public $_registry;
    public $order = null;
    public $shipment;
    public $shipmentId = null;
    public $type;
    public $type2;
    public $paymentmethod;
    public $shipmentTotalPrice;
    public $shippingAddress;
    public $defConfParams;
    public $defPackageParams;
    public $shipByUps;
    public $shipByUpsCode;
    public $shipByUpsMethodName;
    public $upsAccounts;
    public $label = [];
    public $label2 = [];
    public $storeId;
    public $pickup;

    public $negotiatedRates;
    public $ratesTax;
    public $totalWeight;
    public $error;
    public $sku = [];

    protected $messageManager;
    protected $_currencyFactory;
    protected $orderRepository;
    protected $shipmentRepository;
    protected $creditmemoRepository;
    protected $options;
    protected $upsMethod;
    protected $accountCollection;
    protected $defaultdimensionsset;
    protected $labelsModel;
    protected $labelFactory;
    protected $pickupModel;
    protected $productRepository;
    protected $conformity;
    protected $upsFactory;
    protected $convertOrder;
    protected $upsAccount;
    /**
     * @var \Infomodus\Upslabel\Model\Config\Defaultaddress
     */
    private $addresses;

    /**
     * Handy constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Config $config
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository
     * @param \Magento\Sales\Model\Order\CreditmemoRepository $creditmemoRepository
     * @param \Infomodus\Upslabel\Model\Config\Options $options
     * @param \Infomodus\Upslabel\Model\Config\Upsmethod $upsMethod
     * @param \Infomodus\Upslabel\Model\ResourceModel\Account\Collection $accountCollection
     * @param \Infomodus\Upslabel\Model\Config\Defaultdimensionsset $defaultdimensionsset
     * @param \Infomodus\Upslabel\Model\Config\Defaultaddress $addresses
     * @param \Infomodus\Upslabel\Model\Items $labelsModel
     * @param \Infomodus\Upslabel\Model\ItemsFactory $labelFactory
     * @param \Infomodus\Upslabel\Model\Pickup $pickupModel
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Infomodus\Upslabel\Model\Conformity $conformity
     * @param \Infomodus\Upslabel\Model\UpsFactory $upsFactory
     * @param \Magento\Sales\Model\Convert\OrderFactory $convertOrder
     * @param \Infomodus\Upslabel\Model\Account $upsAccount
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Infomodus\Upslabel\Helper\Config $config,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Magento\Sales\Model\Order\CreditmemoRepository $creditmemoRepository,
        \Infomodus\Upslabel\Model\Config\Options $options,
        \Infomodus\Upslabel\Model\Config\Upsmethod $upsMethod,
        \Infomodus\Upslabel\Model\ResourceModel\Account\Collection $accountCollection,
        \Infomodus\Upslabel\Model\Config\Defaultdimensionsset $defaultdimensionsset,
        \Infomodus\Upslabel\Model\Config\Defaultaddress $addresses,
        \Infomodus\Upslabel\Model\Items $labelsModel,
        \Infomodus\Upslabel\Model\ItemsFactory $labelFactory,
        \Infomodus\Upslabel\Model\Pickup $pickupModel,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Infomodus\Upslabel\Model\Conformity $conformity,
        \Infomodus\Upslabel\Model\UpsFactory $upsFactory,
        \Magento\Sales\Model\Convert\OrderFactory $convertOrder,
        \Infomodus\Upslabel\Model\Account $upsAccount
    )
    {
        $this->_registry = $registry;
        parent::__construct($context);
        $this->_context = $context;
        $this->objectManager = $objectManager;
        $this->_conf = $config;
        $this->messageManager = $messageManager;
        $this->_currencyFactory = $currencyFactory;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->options = $options;
        $this->upsMethod = $upsMethod;
        $this->accountCollection = $accountCollection;
        $this->defaultdimensionsset = $defaultdimensionsset;
        $this->labelsModel = $labelsModel;
        $this->labelFactory = $labelFactory;
        $this->pickupModel = $pickupModel;
        $this->productRepository = $productRepository;
        $this->conformity = $conformity;
        $this->upsFactory = $upsFactory;
        $this->convertOrder = $convertOrder;
        $this->upsAccount = $upsAccount;
        $this->addresses = $addresses;
    }

    public function intermediate($order, $type, $shipmentId = null)
    {
        $this->defConfParams = [];
        $this->shipmentId = $shipmentId;
        unset($shipmentId);
        if ($order !== null) {
            if (!is_numeric($order)) {
                $this->order = $order;
            } else {
                $this->order = $this->orderRepository->get($order);
            }
        } elseif ($this->shipmentId !== null) {
            if ($type !== 'refund') {
                $this->order = $this->shipmentRepository->get($this->shipmentId)->getOrder();
            } else {
                $this->order = $this->shipment = $this->creditmemoRepository->get($this->shipmentId)->getOrder();
            }
        }
        unset($order);
        $this->type = $type;
        unset($type);

        $this->storeId = $this->order->getStoreId();
        $this->paymentmethod = "";
        if (is_object($this->order->getPayment())) {
            $this->paymentmethod = $this->order->getPayment()->getData();
            $this->paymentmethod = $this->paymentmethod['method'];
        }
        $this->shippingAddress = $this->order->getShippingAddress();
        $isAccessPoint = false;
        $modelAccessPoint = null;
        if ($this->_conf->isModuleOutputEnabled("Infomodus_Upsap") && $this->_conf->getStoreConfig('carriers/upsap/active', $this->storeId) == 1) {
            $modelAccessPoint = $this->objectManager->create('Infomodus\Upsap\Model\Points')->getCollection()->addFieldToFilter('order_id', $this->order->getId())->getFirstItem()->getData();
            if (count($modelAccessPoint) > 0) {
                $isAccessPoint = true;
            }
        }

        if (!$this->shippingAddress || $isAccessPoint === true) {
            $this->shippingAddress = $this->order->getBillingAddress();
        }

        if ($this->shipmentId !== null) {
            if ($this->type != 'refund' || $this->order->hasCreditmemos() == 0) {
                $this->shipment = $this->shipmentRepository->get($this->shipmentId);
            } else {
                $creditmemo = $this->creditmemoRepository->get($this->shipmentId);
                if ($creditmemo && $creditmemo->getOrderId() == $this->order->getId()) {
                    $this->shipment = $creditmemo;
                } else {
                    $this->shipment = $this->shipmentRepository->get($this->shipmentId);
                }
            }

            $shipmentAllItems = $this->shipment->getAllItems();
        } else {
            $shipmentAllItems = $this->order->getAllVisibleItems();
        }

        $totalPrice = 0;
        $this->totalWeight = 0;
        $totalShipmentQty = 0;

        $this->allowedCurrencies = $this->_currencyFactory->create()->getConfigAllowCurrencies();
        $baseCurrencyCode = $this->_conf->getStoreConfig('currency/options/base', $this->storeId);
        $baseOrderBaseCurrencyCode = $this->order->getBaseCurrencyCode();
        $responseCurrencyCode = $this->_conf->getStoreConfig('upslabel/ratepayment/currencycode', $this->storeId);
        $currencyKoef = 1;
        if ($responseCurrencyCode != $baseOrderBaseCurrencyCode && $baseCurrencyCode == $baseOrderBaseCurrencyCode) {
            if (in_array($responseCurrencyCode, $this->allowedCurrencies)) {
                $currencyKoef = $this->_getBaseCurrencyKoef($baseCurrencyCode, $responseCurrencyCode);
            }
        }

        $this->sku = [];
        $pi = 1;
        foreach ($shipmentAllItems as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                if ($item->getOrderItemId()) {
                    $item = $this->order->getItemById($item->getOrderItemId());
                }

                if (!$item) {
                    continue;
                }
                $itemData = $item->getData();
                $this->sku[] = $itemData['sku'];
                if (!isset($itemData['qty'])) {
                    $itemData['qty'] = $itemData['qty_ordered'];
                }

                if (isset($itemData['weight'])) {
                    $this->totalWeight += $itemData['weight'] * $itemData['qty'];
                }

                $itemPrice = ($item->getBasePrice() /*- $item->getBaseDiscountAmount() / $itemData['qty']*/) * $currencyKoef;
                $totalPrice += $itemPrice * $itemData['qty'];
                $totalShipmentQty += $itemData['qty'];

                if ($this->_conf->getStoreConfig('upslabel/paperless/enable', $this->storeId) == 1) {
                    $productOrigin = $this->productRepository->getById($itemData["product_id"])->getData();
                    $commodityCode = trim($this->_conf->getStoreConfig('upslabel/paperless/international_commodity_id', $this->storeId));

                    $scheduleBNumber = $this->_conf->getStoreConfig('upslabel/paperless/schedule_b', $this->storeId) == 1 ? (
                    $this->_conf->getStoreConfig('upslabel/paperless/schedule_b_number_attribute_id', $this->storeId) != '' ? (
                    (isset($productOrigin[$this->_conf->getStoreConfig('upslabel/paperless/schedule_b_number_attribute_id', $this->storeId)]) && $productOrigin[$this->_conf->getStoreConfig('upslabel/paperless/schedule_b_number_attribute_id', $this->storeId)] != '') ? $productOrigin[$this->_conf->getStoreConfig('upslabel/paperless/schedule_b_number_attribute_id', $this->storeId)] : (
                    $this->_conf->getStoreConfig('upslabel/paperless/schedule_b_number_default', $this->storeId)
                    )
                    ) : $this->_conf->getStoreConfig('upslabel/paperless/schedule_b_number_default', $this->storeId)
                    ) : '';
                    $scheduleBUnit = $this->_conf->getStoreConfig('upslabel/paperless/schedule_b', $this->storeId) == 1 ? (
                    $this->_conf->getStoreConfig('upslabel/paperless/schedule_b_unitOfMeasurement_attribute_id', $this->storeId) != '' ? (
                    (isset($productOrigin[$this->_conf->getStoreConfig('upslabel/paperless/schedule_b_unitOfMeasurement_attribute_id', $this->storeId)]) && $productOrigin[$this->_conf->getStoreConfig('upslabel/paperless/schedule_b_unitOfMeasurement_attribute_id', $this->storeId)] != '') ? $productOrigin[$this->_conf->getStoreConfig('upslabel/paperless/schedule_b_unitOfMeasurement_attribute_id', $this->storeId)] : (
                    $this->_conf->getStoreConfig('upslabel/paperless/schedule_b_unitOfMeasurement', $this->storeId)
                    )
                    ) : $this->_conf->getStoreConfig('upslabel/paperless/schedule_b_unitOfMeasurement', $this->storeId)
                    ) : '';

                    $this->defConfParams['international_products'][] = [
                        'enabled' => 1,
                        'description' => strlen($this->_conf->getStoreConfig('upslabel/paperless/product_description', $this->storeId)) > 0 ? $this->_conf->getStoreConfig('upslabel/paperless/product_description', $this->storeId) : $this->_conf->getStrMaxLength($productOrigin['name'], 35),
                        'country_code' => (isset($productOrigin['country_of_manufacture']) && $productOrigin['country_of_manufacture'] != '') ? $productOrigin['country_of_manufacture'] : $this->_conf->getStoreConfig('upslabel/paperless/product_origin_country', $this->storeId),
                        'qty' => isset($itemData['qty']) ? (int)$itemData['qty'] : 1,
                        'amount' => round($itemPrice, 2),
                        'unit_of_measurement' => $this->_conf->getStoreConfig('upslabel/paperless/international_unitofmeasurement', $this->storeId),
                        'unit_of_measurement_desc' => $this->_conf->getStoreConfig('upslabel/paperless/international_unitofmeasurementdesc', $this->storeId),
                        'commoditycode' => ($commodityCode !== "" && isset($productOrigin[$commodityCode])) ? $productOrigin[$commodityCode] : "",
                        'partnumber' => $pi,
                        'scheduleB_number' => $scheduleBNumber,
                        'scheduleB_unit' => $scheduleBUnit,
                    ];
                }
                $pi++;
            }
        }

        $this->sku = implode(",", $this->sku);
        $totalQty = 0;
        foreach ($this->order->getAllVisibleItems() as $item) {
            $itemData = $item->getData();
            $totalQty += $itemData['qty_ordered'];
        }

        $this->upsAccounts = ["Shipper"];
        $upsAcctModel = $this->accountCollection->load();
        foreach ($upsAcctModel as $u1) {
            $this->upsAccounts[$u1->getId()] = $u1->getCompanyname();
        }

        if (count($shipmentAllItems) != count($this->order->getAllVisibleItems()) && count($shipmentAllItems) != count($this->order->getAllItems())) {
            if ($this->_conf->getStoreConfig('upslabel/ratepayment/cod_shipping_cost', $this->storeId) == 0) {
                $this->shipmentTotalPrice = $totalPrice;
            } else {
                $this->shipmentTotalPrice = $totalPrice + $this->order->getShippingAmount();
            }
        } else {
            if ($this->_conf->getStoreConfig('upslabel/ratepayment/cod_shipping_cost', $this->storeId) == 0) {
                $this->shipmentTotalPrice = $this->order->getGrandTotal() - $this->order->getShippingAmount();
            } else {
                $this->shipmentTotalPrice = $this->order->getGrandTotal();
            }
        }

        $this->defConfParams['upsaccount'] = $this->_conf->getStoreConfig('upslabel/ratepayment/third_party', $this->storeId);

        $ship_method = $this->order->getShippingMethod();

        $address = $this->addresses->getAddressesById($this->_conf->getStoreConfig('upslabel/shipping/defaultshipfrom', $this->storeId));

        if (empty($address)) {
            return false;
        }

        $shippingInternational = ($this->shippingAddress->getCountryId() == $address->getCountry()) ? 0 : 1;
        $this->shipByUps = preg_replace("/^ups_.{1,4}$/", 'ups', $ship_method);

        if ($this->shipByUps == 'ups') {
            $this->shipByUpsCode = $this->upsMethod->getUpsMethodNumber(preg_replace("/^ups_(.{2,4})$/", '$1', $ship_method));
            $this->shipByUpsMethodName = $this->upsMethod->getUpsMethodName($this->shipByUpsCode);
            $this->defConfParams['serviceCode'] = $this->shipByUpsCode;
        } else if ($this->shipByUps = preg_replace("/^upsap_.{1,100}$/", 'upsap', $ship_method) == 'upsap') {
            $this->shipByUps = 'ups';
            $this->shipByUpsCode = explode("_", $ship_method);
            $apModel = $this->objectManager->create('Infomodus\Upsap\Model\Items')->load($this->shipByUpsCode[1]);
            if ($apModel) {
                $this->shipByUpsCode = $apModel->getUpsmethodId();
                $this->defConfParams['serviceCode'] = $this->shipByUpsCode;
                $this->shipByUpsMethodName = $this->upsMethod->getUpsMethodName($this->shipByUpsCode);
            }
        } else if ($this->shipByUps = preg_replace("/^caship_.{1,100}$/", 'caship', $ship_method) == 'caship') {
            $this->shipByUps = 'ups';
            $this->shipByUpsCode = explode("_", $ship_method);
            $apModel = $this->objectManager->create('Infomodus\Caship\Model\Items')->load($this->shipByUpsCode[1]);
            if ($apModel && ($apModel->getCompanyType() == 'ups' || $apModel->getCompanyType() == 'upsinfomodus')) {
                $this->shipByUpsCode = $apModel->getUpsmethodId();
                $this->defConfParams['serviceCode'] = $this->shipByUpsCode;
                $this->shipByUpsMethodName = $this->upsMethod->getUpsMethodName($this->shipByUpsCode);
            }
        } else if ($this->_conf->getStoreConfig('upslabel/shipping/shipping_method_native', $this->storeId) == 1) {
            $modelConformity = $this->conformity->getCollection()
                ->addFieldToFilter('method_id', $ship_method)
                ->addFieldToFilter('store_id', $this->storeId ? $this->storeId : 1)
                ->getSelect()->where('CONCAT(",", country_ids, ",") LIKE "%,' . $this->shippingAddress->getCountryId() . ',%"')->query()->fetch();
            if ($modelConformity && count($modelConformity) > 0) {
                $this->defConfParams['serviceCode'] = $modelConformity["upsmethod_id"];
            }
        }

        if (!isset($this->defConfParams['serviceCode'])) {
            $this->defConfParams['serviceCode'] = $shippingInternational == 0 ? $this->_conf->getStoreConfig('upslabel/shipping/defaultshipmentmethod', $this->storeId) : $this->_conf->getStoreConfig('upslabel/shipping/defaultshipmentmethodworld', $this->storeId);
        }

        if ($this->totalWeight <= 0) {
            $this->totalWeight = (float)str_replace(',', '.', $this->_conf->getStoreConfig('upslabel/weightdimension/defweigth', $this->storeId));
            if ($this->totalWeight == '' || $this->totalWeight <= 0) {
                $this->messageManager->addErrorMessage("Some of the products are missing their weight information. Please fill the weight for all products or enter a default value from the \"Weight and Dimensions\" section of the UPS module configuration.");
            }
        }

        $this->defConfParams['shipper_no'] = $this->_conf->getStoreConfig('upslabel/shipping/defaultshipper', $this->storeId);
        $this->defConfParams['shipfrom_no'] = $this->_conf->getStoreConfig('upslabel/shipping/defaultshipfrom', $this->storeId);
        $this->defConfParams['testing'] = $this->_conf->getStoreConfig('upslabel/testmode/testing', $this->storeId);
        $this->defConfParams['addtrack'] = $this->_conf->getStoreConfig('upslabel/shipping/addtrack', $this->storeId);
        if ($this->_conf->getStoreConfig('upslabel/shipping/shipmentdescription', $this->storeId) != 4) {
            $this->defConfParams['shipmentdescription'] = $this->_conf->getStoreConfig('upslabel/shipping/shipmentdescription', $this->storeId) == 1 ? ($this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname() . ', ' . $this->order->getIncrementId()) : ($this->_conf->getStoreConfig('upslabel/shipping/shipmentdescription', $this->storeId) == 2 ? $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname() : ($this->_conf->getStoreConfig('upslabel/shipping/shipmentdescription', $this->storeId) !== '' ? __('Order Id') . ': ' . $this->order->getIncrementId() : ''));
        } else {
            $this->defConfParams['shipmentdescription'] = $this->macropaste($this->_conf->getStoreConfig('upslabel/shipping/shipmentdescription_custom', $this->storeId));
        }

        $this->defConfParams['currencycode'] = $this->_conf->getStoreConfig('upslabel/ratepayment/currencycode', $this->storeId);
        $this->defConfParams['shipmentcharge'] = $this->_conf->getStoreConfig('upslabel/ratepayment/dytytaxinternational', $this->storeId);
        $this->defConfParams['cod'] = $this->_conf->getStoreConfig('upslabel/ratepayment/cod', $this->storeId) == 1 ? 1 : (($this->paymentmethod == 'cashondelivery' || $this->paymentmethod == 'phoenix_cashondelivery') ? 1 : 0);
        $this->defConfParams['codmonetaryvalue'] = $this->shipmentTotalPrice;
        $this->defConfParams['codfundscode'] = 1;
        $this->defConfParams['invoicelinetotalyesno'] = $this->_conf->getStoreConfig('upslabel/ratepayment/invoicelinetotal', $this->storeId);
        $this->defConfParams['invoicelinetotal'] = $this->shipmentTotalPrice;
        $this->defConfParams['carbon_neutral'] = $this->_conf->getStoreConfig('upslabel/ratepayment/carbon_neutral', $this->storeId);
        $this->defConfParams['default_return'] = ($this->_conf->getStoreConfig('upslabel/return/default_return', $this->storeId) == 0 || $this->_conf->getStoreConfig('upslabel/return/default_return_amount', $this->storeId) > $this->shipmentTotalPrice) ? 0 : 1;
        $this->defConfParams['default_return_servicecode'] = $this->_conf->getStoreConfig('upslabel/return/default_return_method', $this->storeId);
        $this->defConfParams['qvn'] = $this->_conf->getStoreConfig('upslabel/quantum/qvn', $this->storeId);
        $this->defConfParams['qvn_code'] = explode(",", $this->_conf->getStoreConfig('upslabel/quantum/qvn_code', $this->storeId));
        $this->defConfParams['qvn_email_shipper'] = $this->_conf->getStoreConfig('upslabel/quantum/qvn_email_shipper', $this->storeId);
        $this->defConfParams['qvn_lang'] = $this->_conf->getStoreConfig('upslabel/quantum/qvn_lang', $this->storeId);
        $this->defConfParams['adult'] = $this->_conf->getStoreConfig('upslabel/quantum/adult', $this->storeId);
        $this->defConfParams['weightunits'] = $this->_conf->getStoreConfig('upslabel/weightdimension/weightunits', $this->storeId);
        $this->defConfParams['includedimensions'] = $this->_conf->getStoreConfig('upslabel/weightdimension/includedimensions', $this->storeId);
        $this->defConfParams['unitofmeasurement'] = $this->_conf->getStoreConfig('upslabel/weightdimension/unitofmeasurement', $this->storeId);

        $this->defConfParams['residentialaddress'] = 0;

        if ($this->_conf->getStoreConfig('upslabel/shipping/dest_type', $this->storeId) == 3) {
            $this->defConfParams['residentialaddress'] = strlen($this->shippingAddress->getCompany()) > 0 ? 0 : 1;
        } else if ($this->_conf->getStoreConfig('upslabel/shipping/dest_type', $this->storeId) == 1) {
            $this->defConfParams['residentialaddress'] = 1;
        } else if ($this->_conf->getStoreConfig('upslabel/shipping/dest_type', $this->storeId) == 2) {
            $this->defConfParams['residentialaddress'] = 2;
        }

        $this->defConfParams['shiptocompanyname'] = strlen($this->shippingAddress->getCompany()) > 0 ? $this->shippingAddress->getCompany() : $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname();
        $this->defConfParams['shiptoattentionname'] = $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname();
        $this->defConfParams['shiptophonenumber'] = $this->_conf->escapePhone($this->shippingAddress->getTelephone());
        $addressLine1 = $this->shippingAddress->getStreet();
        $this->defConfParams['shiptoaddressline1'] = is_array($addressLine1) && array_key_exists(0, $addressLine1) ? $addressLine1[0] : $addressLine1;
        $this->defConfParams['shiptoaddressline2'] = (is_array($addressLine1) && isset($addressLine1[1])) ? $addressLine1[1] : '';
        $this->defConfParams['shiptocity'] = $this->shippingAddress->getCity();
        $this->defConfParams['shiptostateprovincecode'] = $this->shippingAddress->getRegion();
        $this->defConfParams['shiptopostalcode'] = $this->shippingAddress->getPostcode();
        $this->defConfParams['shiptocountrycode'] = $this->shippingAddress->getCountryId();
        $this->defConfParams['qvn_email_shipto'] = $this->shippingAddress->getEmail();
        $this->defConfParams['saturday_delivery'] = $this->_conf->getStoreConfig('upslabel/shipping/saturday_delivery', $this->storeId);

        $this->defConfParams['movement_reference_number_enabled'] = $this->_conf->getStoreConfig('upslabel/shipping/movement_min_price', $this->storeId) <= $this->shipmentTotalPrice ? 1 : 0;
        $this->defConfParams['movement_reference_number'] = $this->_conf->getStoreConfig('upslabel/shipping/movement_reference_number', $this->storeId);


        $this->defConfParams['international_invoice'] = $this->_conf->getStoreConfig('upslabel/paperless/enable', $this->storeId);
        $this->defConfParams['international_comments'] = $this->_conf->getStoreConfig('upslabel/paperless/international_comments', $this->storeId);
        $this->defConfParams['international_invoicenumber'] = $this->order->getIncrementId();
        $this->defConfParams['international_invoicedate'] = date("Y-m-d", time());
        $this->defConfParams['international_reasonforexport'] = $this->_conf->getStoreConfig('upslabel/paperless/reasonforexport', $this->storeId);
        $this->defConfParams['international_purchaseordernumber'] = $this->order->getIncrementId();
        $this->defConfParams['international_termsofshipment'] = $this->_conf->getStoreConfig('upslabel/paperless/international_termsofshipment', $this->storeId);
        $this->defConfParams['declaration_statement'] = $this->_conf->getStoreConfig('upslabel/paperless/declaration_statement', $this->storeId);
        /*$this->defConfParams['international_sold_to'] = $this->_conf->getStoreConfig('upslabel/paperless/international_sold_to', $this->storeId);*/

        $this->defConfParams['accesspoint'] = 0;
        if ($this->_conf->getStoreConfig('carriers/upsap/active', $this->storeId) == 1 && $this->_conf->isModuleOutputEnabled("Infomodus_Upsap")) {
            if (count($modelAccessPoint) > 0) {
                $modelAccessPoint = json_decode($modelAccessPoint['address'], true);
                $this->defConfParams['accesspoint'] = 1;
                $this->defConfParams['accesspoint_type'] = $this->_conf->getStoreConfig('carriers/upsap/type', $this->storeId);
                $this->defConfParams['accesspoint_name'] = $modelAccessPoint['name'];
                $this->defConfParams['accesspoint_atname'] = $modelAccessPoint['name'];
                $this->defConfParams['accesspoint_appuid'] = $modelAccessPoint['appuId'];
                $this->defConfParams['accesspoint_street'] = $modelAccessPoint['addLine1'];
                if (isset($modelAccessPoint['addLine2'])) {
                    $this->defConfParams['accesspoint_street1'] = $modelAccessPoint['addLine2'];
                    if (isset($modelAccessPoint['addLine3'])) {
                        $this->defConfParams['accesspoint_street2'] = $modelAccessPoint['addLine3'];
                    }
                }

                $this->defConfParams['accesspoint_city'] = $modelAccessPoint['city'];
                $this->defConfParams['accesspoint_provincecode'] = $modelAccessPoint['state'];
                $this->defConfParams['accesspoint_postal'] = $modelAccessPoint['postal'];
                $this->defConfParams['accesspoint_country'] = $modelAccessPoint['country'];
                $address = $this->addresses->getAddressesById($this->defConfParams['shipfrom_no']);
                if (
                    $this->defConfParams['accesspoint_type'] !== '01'
                    || strpos(
                        $this->_conf->getStoreConfig('general/country/eu_countries', $this->storeId),
                        $this->defConfParams['shiptocountrycode']
                    ) === false
                    || strpos(
                        $this->_conf->getStoreConfig('general/country/eu_countries', $this->storeId),
                        (!empty($address) ? $address->getCountry() : '')
                    ) === false
                ) {
                    $this->defConfParams['cod'] = 0;
                }
            }
        }

        $attributeCodeWidth = $this->_conf->getStoreConfig('upslabel/weightdimension/multipackes_attribute_width', $this->storeId) ?
            $this->_conf->getStoreConfig('upslabel/weightdimension/multipackes_attribute_width', $this->storeId) : 'width';
        $attributeCodeHeight = $this->_conf->getStoreConfig('upslabel/weightdimension/multipackes_attribute_height', $this->storeId) ?
            $this->_conf->getStoreConfig('upslabel/weightdimension/multipackes_attribute_height', $this->storeId) : 'height';
        $attributeCodeLength = $this->_conf->getStoreConfig('upslabel/weightdimension/multipackes_attribute_length', $this->storeId) ?
            $this->_conf->getStoreConfig('upslabel/weightdimension/multipackes_attribute_length', $this->storeId) : 'length';

        /* Multi package */
        $dimensionSets = $this->defaultdimensionsset->toOptionObjects();

        if (
            $this->type == 'shipment'
            && $this->_conf->getStoreConfig('upslabel/packaging/frontend_multipackes_enable', $this->storeId) == 1
        ) {
            $i = 0;
            $defParArr_1 = [];
            foreach ($shipmentAllItems as $item) {
                if (!$item->isDeleted() && !$item->getParentItemId()) {
                    $itemData = $item->getData();
                    if (!isset($itemData['qty'])) {
                        $itemData['qty'] = $itemData['qty_ordered'];
                    }

                    if (!isset($itemData['weight'])) {
                        foreach ($this->order->getAllVisibleItems() as $w) {
                            if ($w->getProductId() == $itemData["product_id"]) {
                                $itemData['weight'] = $w->getWeight();
                            }
                        }
                    }

                    $myproduct = $this->productRepository->getById($itemData['product_id'])->getData();

                    if (!isset($itemData['qty'])) {
                        $this->messageManager->addErrorMessage("Product " . $item->getName() . " does not have quantity");
                        return false;
                    }

                    for ($ik = 0; $ik < $itemData['qty']; $ik++) {
                        $is_attribute = 0;
                        if ($this->_conf->getStoreConfig('upslabel/packaging/packages_by_attribute_enable', $this->storeId) == 1) {
                            if (isset($myproduct[$this->_conf->getStoreConfig('upslabel/packaging/packages_by_attribute_code', $this->storeId)])) {
                                $attribute = explode(";", trim($myproduct[$this->_conf->getStoreConfig('upslabel/packaging/packages_by_attribute_code', $this->storeId)], ";"));
                                if (count($attribute) > 1) {
                                    $rvaPrice = $item->getBasePrice() * $currencyKoef;
                                    foreach ($attribute as $v) {
                                        $itemData['weight'] = $v;
                                        $itemData['price'] = round($rvaPrice / count($attribute), 2);
                                        $defParArr_1[$i] = $this->setPackageDefParams($itemData);
                                        $i++;
                                    }
                                    $is_attribute = 1;
                                }
                            }
                        }
                        if ($is_attribute !== 1) {
                            $countProductInBox = 0;
                            try {
                                if ($this->_conf->getStoreConfig('upslabel/weightdimension/dimensions_type', $this->storeId) == 0) {
                                    $packer = new Packer();
                                    $myproduct = $this->productRepository->getById($itemData['product_id'])->getData();

                                    if ($item->getWeight()) {
                                        $myproduct['weight'] = $item->getWeight();

                                        $myproduct = $this->getProductSizes(
                                            $item,
                                            $itemData,
                                            $myproduct,
                                            $packer,
                                            $attributeCodeWidth,
                                            $attributeCodeHeight,
                                            $attributeCodeLength,
                                            $currencyKoef
                                        );
                                        if ($myproduct === false) {
                                            $this->messageManager->addErrorMessage("Product " . $item->getName() . " does not have width or height or length");
                                            break;
                                        } else {
                                            $countProductInBox++;
                                        }

                                        if ($countProductInBox > 0) {
                                            $packer->addBox(new PackerBox(
                                                'def_box',
                                                1000,
                                                1000,
                                                1000,
                                                0,
                                                1000,
                                                1000,
                                                1000,
                                                150
                                            ));
                                            $packedBoxes = $packer->pack();
                                            if (count($packedBoxes) > 0) {
                                                foreach ($packedBoxes as $packedBox) {
                                                    $itemDataTwo = [];
                                                    $itemDataTwo['width'] = $packedBox->getUsedWidth();
                                                    $itemDataTwo['length'] = $packedBox->getUsedLength();
                                                    $itemDataTwo['height'] = $packedBox->getUsedDepth();
                                                    $itemDataTwo['weight'] = $packedBox->getWeight();
                                                    $itemsInTheBox = $packedBox->getItems();
                                                    $itemDataTwo['price'] = 0;
                                                    foreach ($itemsInTheBox as $itemBox) {
                                                        $itemDataTwo['price'] += $itemBox->getDescription();
                                                    }

                                                    $defParArr_1[$i] = $this->setPackageDefParams($itemDataTwo);
                                                    $i++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $defaultBox = $this->_conf->getStoreConfig('upslabel/weightdimension/default_dimensions_box', $this->storeId);

                                    if (!empty($defaultBox) && !empty($dimensionSets[$defaultBox])) {
                                        $v = $dimensionSets[$defaultBox];
                                        $itemDataTwo = array();
                                        $itemDataTwo['width'] = $v->getOuterWidth();
                                        $itemDataTwo['length'] = $v->getOuterLengths();
                                        $itemDataTwo['height'] = $v->getOuterHeight();
                                        $defParArr_1[$i] = $this->setPackageDefParams($itemDataTwo);
                                        $i++;
                                    }
                                }
                            } catch (ItemTooLargeException $e) {
                                $this->_conf->log($e->getMessage());
                            }
                        }
                    }
                }
            }
            $this->defPackageParams = $defParArr_1;
        } else {
            $this->defPackageParams = [];
            $i = 0;
            $rvaShipmentTotalPrice = $this->shipmentTotalPrice;
            if ($this->_conf->getStoreConfig('upslabel/packaging/packages_by_attribute_enable', $this->storeId) == 1 && $this->type == 'shipment') {
                foreach ($shipmentAllItems as $item) {
                    if (!$item->isDeleted() && !$item->getParentItemId()) {
                        $itemData = $item->getData();
                        if (!isset($itemData['qty'])) {
                            $itemData['qty'] = $itemData['qty_ordered'];
                        }

                        if (!isset($itemData['weight'])) {
                            foreach ($this->order->getAllVisibleItems() as $w) {
                                if ($w->getProductId() == $itemData["product_id"]) {
                                    $itemData['weight'] = $w->getWeight();
                                }
                            }
                        }

                        $itemData2 = $itemData;
                        $myproduct = $this->productRepository->getById($itemData['product_id'])->getData();
                        for ($ik = 0; $ik < $itemData['qty']; $ik++) {
                            if (isset($myproduct[$this->_conf->getStoreConfig('upslabel/packaging/packages_by_attribute_code', $this->storeId)])) {
                                $attribute = explode(";", trim($myproduct[$this->_conf->getStoreConfig('upslabel/packaging/packages_by_attribute_code', $this->storeId)], ";"));
                                if (count($attribute) > 1) {
                                    foreach ($attribute as $v) {
                                        $this->totalWeight = $this->totalWeight - $itemData2['weight'];
                                        $itemData['price'] = round($item->getBasePrice() * $currencyKoef / count($attribute), 2);
                                        $itemData['weight'] = $v;
                                        $this->defPackageParams[$i] = $this->setPackageDefParams($itemData);
                                        $i++;
                                    }

                                    $rvaShipmentTotalPrice = $rvaShipmentTotalPrice - $item->getBasePrice() * $currencyKoef;
                                }
                            }
                        }
                    }
                }
            }

            if ($this->totalWeight > 0) {
                $countProductInBox = 0;
                if ($this->type == 'shipment' || $this->type == 'invert') {
                    if (count($dimensionSets) > 0) {
                        try {
                            if ($this->_conf->getStoreConfig('upslabel/weightdimension/dimensions_type', $this->storeId) == 0) {
                                $packer = new Packer();
                                foreach ($shipmentAllItems as $item) {
                                    $itemData = $item->getData();
                                    if (!isset($itemData['qty'])) {
                                        $itemData['qty'] = $itemData['qty_ordered'];
                                    }

                                    $myproduct = $this->productRepository->getById($itemData['product_id'])->getData();

                                    if ($item->getWeight() && (!isset($myproduct['weight']) || !$myproduct['weight'])) {
                                        $myproduct['weight'] = $item->getWeight();
                                    }

                                    for ($ik = 0; $ik < $itemData['qty']; $ik++) {
                                        $myproduct = $this->getProductSizes(
                                            $item,
                                            $itemData,
                                            $myproduct,
                                            $packer,
                                            $attributeCodeWidth,
                                            $attributeCodeHeight,
                                            $attributeCodeLength,
                                            $currencyKoef
                                        );
                                        if ($myproduct === false) {
                                            $countProductInBox = 0;
                                            $this->messageManager->addErrorMessage("Product " . $item->getName() . " does not have width or height or length");
                                            break;
                                        } else {
                                            $countProductInBox++;
                                        }
                                    }

                                    if ($countProductInBox == 0) {
                                        break;
                                    }
                                }

                                if ($countProductInBox > 0) {
                                    foreach ($dimensionSets as $v) {
                                        if (!empty($v)) {
                                            $packer->addBox(new PackerBox(
                                                $v->getId(),
                                                $v->getOuterWidth(),
                                                $v->getOuterLengths(),
                                                $v->getOuterHeight(),
                                                $v->getEmptyWeight(),
                                                $v->getWidth(),
                                                $v->getLengths(),
                                                $v->getHeight(),
                                                $v->getMaxWeight()
                                            ));
                                        }
                                    }

                                    $packedBoxes = $packer->pack();
                                    if (count($packedBoxes) > 0) {
                                        foreach ($packedBoxes as $packedBox) {
                                            $itemData = [];
                                            $boxType = $packedBox->getBox();
                                            $itemData['width'] = $boxType->getOuterWidth();
                                            $itemData['length'] = $boxType->getOuterLength();
                                            $itemData['height'] = $boxType->getOuterDepth();
                                            $itemData['weight'] = $packedBox->getWeight();
                                            $itemsInTheBox = $packedBox->getItems();
                                            $itemData['price'] = 0;
                                            foreach ($itemsInTheBox as $itemBox) {
                                                $itemData['price'] += $itemBox->getDescription();
                                            }

                                            $this->defPackageParams[$i] = $this->setPackageDefParams($itemData);
                                            $i++;
                                        }
                                    } else {
                                        $countProductInBox = 0;
                                    }
                                }
                            } else {
                                $defaultBox = $this->_conf->getStoreConfig('upslabel/weightdimension/default_dimensions_box', $this->storeId);
                                if (!empty($defaultBox) && !empty($dimensionSets[$defaultBox])) {
                                    $v = $dimensionSets[$defaultBox];
                                    $itemData = array();
                                    $itemData['width'] = $v->getOuterWidth();
                                    $itemData['length'] = $v->getOuterLengths();
                                    $itemData['height'] = $v->getOuterHeight();
                                    $this->defPackageParams[$i] = $this->setPackageDefParams($itemData);
                                    $i++;
                                    $countProductInBox = 1;
                                }
                            }
                        } catch (ItemTooLargeException $e) {
                            $countProductInBox = 0;
                            $this->_conf->log($e->getMessage());
                        }
                    }
                }

                if ($countProductInBox == 0) {
                    $this->defPackageParams[$i] = $this->setPackageDefParams(null);
                }
            }
        }

        return true;
    }

    public function getProductSizes($item, $itemData, $product, &$packer, $attributeWidth, $attributeHeight, $attributeLength, $currencyKoef)
    {
        $children = $item->getChildrenItems();

        $isSize = 0;
        if (
            isset($product[$attributeWidth]) && $product[$attributeWidth] != "" && $product[$attributeWidth] > 0
            && isset($product[$attributeHeight]) && $product[$attributeHeight] != "" && $product[$attributeHeight] > 0
            && isset($product[$attributeLength]) && $product[$attributeLength] != "" && $product[$attributeLength] > 0
        ) {
            $packer->addItem(
                new PackerItem(
                    $item->getBasePrice() * $currencyKoef,
                    $product[$attributeWidth],
                    $product[$attributeLength],
                    $product[$attributeHeight],
                    $product['weight'],
                    true
                )
            );
            $isSize++;
        }

        if ($children && count($children) > 0) {
            foreach ($children as $child) {
                $productChild = $this->productRepository->getById($child->getProduct()->getId())->getData();

                if (
                    isset($productChild[$attributeWidth]) && $productChild[$attributeWidth] != "" && $productChild[$attributeWidth] > 0
                    && isset($productChild[$attributeHeight]) && $productChild[$attributeHeight] != "" && $productChild[$attributeHeight] > 0
                    && isset($productChild[$attributeLength]) && $productChild[$attributeLength] != "" && $productChild[$attributeLength] > 0
                ) {
                    $packer->addItem(
                        new PackerItem(
                            0,
                            $productChild[$attributeWidth],
                            $productChild[$attributeLength],
                            $productChild[$attributeHeight],
                            0.001,
                            true
                        )
                    );
                    $isSize++;
                }
            }
        }

        if ($isSize > 0) {
            return $product;
        }

        return false;
    }

    public
    function setPackageDefParams($itemData = null)
    {
        $defParArr_1['packagingtypecode'] = $this->_conf->getStoreConfig('upslabel/packaging/packagingtypecode');
        $defParArr_1['packagingdescription'] = $this->macropaste($this->_conf->getStoreConfig('upslabel/packaging/packagingdescription', $this->storeId));
        $defParArr_1['packagingreferencenumbercode'] = $this->_conf->getStoreConfig('upslabel/packaging/packagingreferencenumbercode', $this->storeId);
        $defParArr_1['packagingreferencebarcode'] = $this->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode', $this->storeId);
        $defParArr_1['packagingreferencenumbervalue'] = $this->macropaste($this->_conf->getStoreConfig('upslabel/packaging/packagingreferencenumbervalue', $this->storeId));
        $defParArr_1['packagingreferencenumbercode2'] = $this->_conf->getStoreConfig('upslabel/packaging/packagingreferencenumbercode2', $this->storeId);
        $defParArr_1['packagingreferencebarcode2'] = $this->_conf->getStoreConfig('upslabel/packaging/packagingreferencebarcode2', $this->storeId);
        $defParArr_1['packagingreferencenumbervalue2'] = $this->macropaste($this->_conf->getStoreConfig('upslabel/packaging/packagingreferencenumbervalue2', $this->storeId));
        $defParArr_1['weight'] = ($itemData !== null && isset($itemData['weight'])) ? $itemData['weight'] : $this->totalWeight;
        $defParArr_1['packweight'] = round((float)str_replace(',', '.', $this->_conf->getStoreConfig('upslabel/weightdimension/packweight', $this->storeId)), 1) > 0 ? round((float)str_replace(',', '.', $this->_conf->getStoreConfig('upslabel/weightdimension/packweight', $this->storeId)), 1) : '0';
        $defParArr_1['additionalhandling'] = $this->_conf->getStoreConfig('upslabel/ratepayment/additionalhandling', $this->storeId);
        $defParArr_1['width'] = $itemData !== null && isset($itemData['width']) ? $itemData['width'] : '';
        $defParArr_1['height'] = $itemData !== null && isset($itemData['height']) ? $itemData['height'] : '';
        $defParArr_1['length'] = $itemData !== null && isset($itemData['length']) ? $itemData['length'] : '';
        $defParArr_1['currencycode'] = $this->_conf->getStoreConfig('upslabel/ratepayment/currencycode', $this->storeId);
        $defParArr_1['cod'] = $this->_conf->getStoreConfig('upslabel/ratepayment/cod', $this->storeId) == 1 ? 1 : (($this->paymentmethod == 'cashondelivery' || $this->paymentmethod == 'phoenix_cashondelivery') ? 1 : 0);
        $defParArr_1['codfundscode'] = 0;
        $defParArr_1['codmonetaryvalue'] = ($itemData !== null && isset($itemData['price'])) ? $itemData['price'] : $this->shipmentTotalPrice;
        $defParArr_1['insuredmonetaryvalue'] = $this->_conf->getStoreConfig('upslabel/ratepayment/insured_automaticaly', $this->storeId) == 1 ? (($itemData !== null && isset($itemData['price'])) ? $itemData['price'] : $this->shipmentTotalPrice) : 0;
        $address = $this->addresses->getAddressesById($this->defConfParams['shipfrom_no']);
        if ($this->defConfParams['accesspoint'] == 1
            && ($this->defConfParams['accesspoint_type'] !== '01'
                || strpos(
                    $this->_conf->getStoreConfig('general/country/eu_countries', $this->storeId),
                    $this->defConfParams['shiptocountrycode']
                ) === false
                || strpos(
                    $this->_conf->getStoreConfig('general/country/eu_countries', $this->storeId),
                    (!empty($address) ? $address->getCountry() : '')
                ) === false)
        ) {
            $defParArr_1['cod'] = 0;
        }

        $defParArr_1['box'] = $defParArr_1['width'] . 'x' . $defParArr_1['height'] . 'x' . $defParArr_1['length'];
        return ($defParArr_1);
    }

    public function getShipRate($lbl)
    {
        return $lbl->getShipRate();
    }

    public function getTIT($lbl, $weightSum)
    {
        return $lbl->timeInTransit($weightSum);
    }

    public function getLabel($order, $type, $shipmentId = null, $params, $isFrontLabel = false)
    {
        if ($this->order === null) {
            $this->order = $order;
        }
        unset($order);
        $this->type = $type;
        unset($type);
        $this->shipmentId = $shipmentId;
        unset($shipmentId);
        if ($this->shipmentId !== null) {
            $this->shipment = $this->shipmentRepository->get($this->shipmentId);
        }

        $this->storeId = $this->order->getStoreId();

        $lbl = $this->upsFactory->create();

        $lbl = $this->setParams($lbl, $params, $params['package']);
        $upsl = [];
        $upsl2 = null;
        if ($this->type == 'shipment' || $this->type == 'invert') {
            $upsl[0] = $lbl->getShip($this->type);
            if (isset($params['default_return']) && $params['default_return'] == 1) {
                $lbl->serviceCode = array_key_exists('default_return_servicecode', $params) ? $params['default_return_servicecode'] : '';
                $upsl2 = [];
                foreach ($params['package'] as $package) {
                    $lbl->packages[0] = $package;
                    $upsl2[] = $lbl->getShipFrom();
                }
            }
        } elseif ($this->type == 'refund') {
            foreach ($params['package'] as $package) {
                $lbl->packages[0] = $package;
                if ($isFrontLabel === true && $lbl->returnServiceCode != 9) {
                    $lbl->returnServiceCode = 8;
                }

                $upsl[] = $lbl->getShipFrom();
            }
        } elseif ($this->type == 'ajaxprice_shipment') {
            $upsl = $lbl->getShipPrice('shipment');
            return $upsl;
        } elseif ($this->type == 'ajaxprice_invert') {
            $upsl = $lbl->getShipPrice('invert');
            return $upsl;
        } elseif ($this->type == 'ajaxprice_refund') {
            foreach ($params['package'] as $package) {
                $lbl->packages[0] = $package;
                $upsl[] = $lbl->getShipPriceFrom('refund');
            }

            return $upsl;
        }

        $this->saveDB($upsl, $upsl2, $params);
        if (!array_key_exists('error', $upsl) || !$upsl['error']) {
            $this->error = [];
        } else {
            $this->error = $upsl;
        }

        return true;
    }

    public
    function setParams($lbl, $params, $packages)
    {
        if ($lbl === null) {
            $lbl = $this->upsFactory->create();
        }

        $configOptions = $this->options;
        $configMethod = $this->upsMethod;
        $lbl->_handy = $this;
        $lbl->packages = $packages;
        $lbl->storeId = $this->storeId;

        $address = $this->addresses->getAddressesById($params['shipper_no']);

        if (empty($address)) {
            return $lbl;
        }

        $lbl->shipmentDescription = isset($params['shipmentdescription']) ? $this->_conf->escapeXML($params['shipmentdescription']) : '';
        $lbl->shipperName = $this->_conf->escapeXML($address->getCompany());
        $lbl->shipperAttentionName = $this->_conf->escapeXML($address->getAttention());
        $lbl->shipperPhoneNumber = $this->_conf->escapeXML($address->getPhone());
        $lbl->shipperAddressLine1 = $this->_conf->escapeXML($address->getStreetOne());
        $lbl->shipperCity = $this->_conf->escapeXML($address->getCity());
        $lbl->shipperStateProvinceCode = $this->_conf->escapeXML($address->getProvinceCode());
        $lbl->shipperPostalCode = $this->_conf->escapeXML($address->getPostalCode());
        $lbl->shipperCountryCode = $this->_conf->escapeXML($address->getCountry());

        if ($lbl->shipperCountryCode == 'US' && $lbl->shipperStateProvinceCode == 'PR') {
            $lbl->shipperCountryCode = 'PR';
            $lbl->shipperStateProvinceCode = '';
        }

        if ($lbl->shipperCountryCode == 'ES') {
            if (in_array(substr($lbl->shipperPostalCode, 0, 2), ['35', '38'])) {
                $lbl->shipperCountryCode = 'IC';
                $lbl->shipperStateProvinceCode = '';
            }
        }

        $lbl->shiptoCompanyName = $this->_conf->escapeXML($params['shiptocompanyname']);
        $lbl->shiptoAttentionName = $this->_conf->escapeXML($params['shiptoattentionname']);
        $lbl->shiptoPhoneNumber = $this->_conf->escapeXML($params['shiptophonenumber']);
        $lbl->shiptoAddressLine1 = trim($this->_conf->escapeXML($params['shiptoaddressline1']));
        $lbl->shiptoAddressLine2 = trim($this->_conf->escapeXML($params['shiptoaddressline2']));
        $lbl->shiptoCity = $this->_conf->escapeXML($params['shiptocity']);
        $lbl->shiptoStateProvinceCode = $this->_conf->escapeXML($configOptions->getProvinceCode($params['shiptostateprovincecode'], $params['shiptocountrycode']));
        $lbl->shiptoPostalCode = $this->_conf->escapeXML($params['shiptopostalcode']);
        $lbl->shiptoCountryCode = $this->_conf->escapeXML($params['shiptocountrycode']);
        $lbl->residentialAddress = isset($params['residentialaddress']) ? $params['residentialaddress'] : '';

        if ($lbl->shiptoCountryCode == 'US' && $lbl->shiptoStateProvinceCode == 'PR') {
            $lbl->shiptoCountryCode = 'PR';
            $lbl->shiptoStateProvinceCode = '';
        }

        if ($lbl->shiptoCountryCode == 'ES') {
            if (in_array(substr($lbl->shiptoPostalCode, 0, 2), ['35', '38'])) {
                $lbl->shiptoCountryCode = 'IC';
                $lbl->shiptoStateProvinceCode = '';
            }
        }

        $address = $this->addresses->getAddressesById($params['shipfrom_no']);

        if (empty($address)) {
            return $lbl;
        }

        $lbl->shipfromCompanyName = $this->_conf->escapeXML($address->getCompany());
        $lbl->shipfromAttentionName = $this->_conf->escapeXML($address->getAttention());
        $lbl->shipfromPhoneNumber = $this->_conf->escapeXML($address->getPhone());
        $lbl->shipfromAddressLine1 = $this->_conf->escapeXML($address->getStreetOne());
        $lbl->shipfromCity = $this->_conf->escapeXML($address->getCity());
        $lbl->shipfromStateProvinceCode = $this->_conf->escapeXML($address->getProvinceCode());
        $lbl->shipfromPostalCode = $this->_conf->escapeXML($address->getPostalCode());
        $lbl->shipfromCountryCode = $this->_conf->escapeXML($address->getCountry());

        if ($lbl->shipfromCountryCode == 'US' && $lbl->shipfromStateProvinceCode == 'PR') {
            $lbl->shipfromCountryCode = 'PR';
            $lbl->shipfromStateProvinceCode = '';
        }

        if ($lbl->shipfromCountryCode == 'ES') {
            if (in_array(substr($lbl->shipfromPostalCode, 0, 2), ['35', '38'])) {
                $lbl->shipfromCountryCode = 'IC';
                $lbl->shipfromStateProvinceCode = '';
            }
        }

        $lbl->serviceCode = array_key_exists('serviceCode', $params) ? $params['serviceCode'] : '';
        $lbl->serviceDescription = $configMethod->getUpsMethodName(array_key_exists('serviceCode', $params) ? $params['serviceCode'] : '');

        $lbl->weightUnits = array_key_exists('weightunits', $params) ? $params['weightunits'] : '';

        $lbl->includeDimensions = array_key_exists('includedimensions', $params) ? $params['includedimensions'] : 0;
        $lbl->unitOfMeasurement = array_key_exists('unitofmeasurement', $params) ? $params['unitofmeasurement'] : '';

        $lbl->codYesNo = array_key_exists('cod', $params) ? $params['cod'] : '';
        $lbl->currencyCode = array_key_exists('currencycode', $params) ? $params['currencycode'] : '';
        $lbl->codMonetaryValue = array_key_exists('codmonetaryvalue', $params) ? $params['codmonetaryvalue'] : '';
        $lbl->codFundsCode = array_key_exists('codfundscode', $params) ? $params['codfundscode'] : '';
        $lbl->carbonNeutral = array_key_exists('carbon_neutral', $params) ? $params['carbon_neutral'] : '';
        $lbl->returnServiceCode = $this->_conf->getStoreConfig('upslabel/return/return_service_code', $this->storeId);

        $lbl->qvnLang = '';
        if (array_key_exists('qvn', $params) && $params['qvn'] > 0 && isset($params['qvn_code'])) {
            $lbl->qvn = 1;
            $lbl->qvnCode = $params['qvn_code'];
            if (isset($params['qvn_lang'])) {
                $lbl->qvnLang = $params['qvn_lang'];
            }
        }

        $lbl->qvnEmailShipper = $params['qvn_email_shipper'];
        $lbl->qvnEmailShipto = $params['qvn_email_shipto'];

        if ($lbl->shipfromCountryCode != $lbl->shiptoCountryCode) {
            $lbl->shipmentcharge = array_key_exists('shipmentcharge', $params) ? $params['shipmentcharge'] : 'shipper';
        }

        if (array_key_exists('invoicelinetotalyesno', $params) && $params['invoicelinetotalyesno'] > 0) {
            $lbl->invoicelinetotal = array_key_exists('invoicelinetotal', $params) ? $params['invoicelinetotal'] : '';
        } else {
            $lbl->invoicelinetotal = '';
        }

        if (isset($params['upsaccount']) && $params['upsaccount'] != 0) {
            $lbl->upsAccount = 1;
            $lbl->accountData = $this->upsAccount->load($params['upsaccount']);
        }

        if ($params['adult'] != 1 || strpos($this->_conf->getStoreConfig('upslabel/quantum/adult_allow_country', $this->storeId), $lbl->shiptoCountryCode) !== false) {
            $lbl->adult = $this->_conf->escapeXML($params['adult']);
        }

        $lbl->movement_reference_number = isset($params['movement_reference_number_enabled']) && isset($params['movement_reference_number']) && $params['movement_reference_number_enabled'] == 1 ? $params['movement_reference_number'] : '';

        $lbl->internationalInvoice = 0;
        if (isset($params['international_invoice']) && $params['international_invoice'] == 1) {
            $lbl->internationalInvoice = $this->_conf->escapeXML($params['international_invoice']);
            $lbl->internationalComments = $this->_conf->escapeXML($params['international_comments']);
            $lbl->internationalInvoicenumber = $this->_conf->escapeXML($params['international_invoicenumber']);
            $lbl->internationalInvoicedate = str_replace("-", "", $this->_conf->escapeXML($params['international_invoicedate']));
            $lbl->internationalReasonforexport = $this->_conf->escapeXML($params['international_reasonforexport']);
            $lbl->internationalTermsofshipment = $this->_conf->escapeXML($params['international_termsofshipment']);
            $lbl->internationalPurchaseordernumber = $this->_conf->escapeXML($params['international_purchaseordernumber']);
            $lbl->declaration_statement = isset($params['declaration_statement']) ? $this->_conf->escapeXML($params['declaration_statement']) : '';
            $lbl->internationalProducts = isset($params['international_products']) ? $params['international_products'] : [];
            /*$lbl->internationalSoldTo = isset($params['international_sold_to'])?$params['international_sold_to']:"shipto";*/
        }

        $lbl->testing = $params['testing'];

        $lbl->saturdayDelivery = !empty($params['saturday_delivery']) ? '<SaturdayDelivery />' : "";
        $lbl->negotiatedRates = $this->_conf->getStoreConfig('upslabel/ratepayment/negotiatedratesindicator', $this->storeId);

        $lbl->accessLicenseNumber = $this->_conf->getStoreConfig('upslabel/credentials/accesslicensenumber', $this->storeId);
        $lbl->userId = $this->_conf->getStoreConfig('upslabel/credentials/userid', $this->storeId);
        $lbl->password = $this->_conf->getStoreConfig('upslabel/credentials/password', $this->storeId);
        $lbl->shipperNumber = $this->_conf->getStoreConfig('upslabel/credentials/shippernumber', $this->storeId);

        if (isset($params['accesspoint'])) {
            $lbl->accesspoint = $params['accesspoint'];
            if ($lbl->accesspoint == 1) {
                $lbl->accesspointType = $this->_conf->escapeXML($params['accesspoint_type']);
                $lbl->accesspointName = isset($params['accesspoint_name']) ? $this->_conf->escapeXML($params['accesspoint_name']) : '';
                $lbl->accesspointAtname = isset($params['accesspoint_atname']) ? $this->_conf->escapeXML($params['accesspoint_atname']) : '';
                $lbl->accesspointAppuid = isset($params['accesspoint_appuid']) ? $this->_conf->escapeXML($params['accesspoint_appuid']) : '';
                $lbl->accesspointStreet = $this->_conf->escapeXML($params['accesspoint_street']);
                $lbl->accesspointStreet1 = isset($params['accesspoint_street1']) ? $this->_conf->escapeXML($params['accesspoint_street1']) : '';
                $lbl->accesspointStreet2 = isset($params['accesspoint_street2']) ? $this->_conf->escapeXML($params['accesspoint_street2']) : '';
                $lbl->accesspointCity = $this->_conf->escapeXML($params['accesspoint_city']);
                $lbl->accesspointProvincecode = isset($params['accesspoint_provincecode']) ? $params['accesspoint_provincecode'] : '';
                $lbl->accesspointPostal = $this->_conf->escapeXML($params['accesspoint_postal']);
                $lbl->accesspointCountry = $this->_conf->escapeXML($params['accesspoint_country']);

                if ($this->_conf->getStoreConfig('carriers/upsap/accesslicensenumber', $this->storeId) != '') {
                    $lbl->accessLicenseNumber = $this->_conf->getStoreConfig('carriers/upsap/accesslicensenumber', $this->storeId);
                    $lbl->userId = $this->_conf->getStoreConfig('carriers/upsap/userid', $this->storeId);
                    $lbl->password = $this->_conf->getStoreConfig('carriers/upsap/password', $this->storeId);
                    $lbl->shipperNumber = $this->_conf->getStoreConfig('carriers/upsap/shippernumber', $this->storeId);
                }
            }
        }

        return $lbl;
    }

    public
    function saveDB($upsl, $upsl2 = null, $params)
    {
        if ($this->order->getId() > 0) {
            $colls2 = $this->labelsModel->getCollection()->addFieldToFilter('order_id', $this->order->getId())/*->addFieldToFilter('type', $this->type)*/
            ->addFieldToFilter('lstatus', 1);
            if (count($colls2) > 0) {
                foreach ($colls2 as $c) {
                    $c->delete();
                }
            }

            $upsl0 = $upsl;
            $upsl = null;

            $toPrintData = [];

            foreach ($upsl0 as $upsl) {
                if (!array_key_exists('error', $upsl) || !$upsl['error']) {
                    foreach ($upsl['arrResponsXML'] as $upsl_one) {
                        $upslabel = $this->labelFactory->create();
                        $upslabel->setTitle('Order ' . $this->order->getIncrementId() . ' TN' . $upsl_one['trackingnumber']);
                        $upslabel->setOrderId($this->order->getId());
                        $upslabel->setOrderIncrementId($this->order->getIncrementId());
                        $upslabel->setType($this->type);
                        $upslabel->setType2($this->type);
                        $upslabel->setTrackingnumber($upsl_one['trackingnumber']);
                        $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                        $upslabel->setShipmentidentificationnumber2($upsl['shipidnumber']);
                        $upslabel->setShipmentdigest($upsl['digest']);
                        if ($upsl_one['type_print'] !== 'link') {
                            $upslabel->setLabelname('label' . $upsl_one['trackingnumber'] . '.' . strtolower($upsl_one['type_print']));
                        } else {
                            $upslabel->setLabelname($upsl_one['graphicImage']);
                        }

                        $upslabel->setStatustext(__('Successfully'));
                        $upslabel->setTypePrint($upsl_one['type_print']);
                        $upslabel->setLstatus(0);
                        $upslabel->setPrice($upsl['price']['price']);
                        $upslabel->setCurrency($upsl['price']['currency']);
                        $upslabel->setStoreId($this->order->getStoreId());
                        if (isset($upsl['inter_invoice']) && $upsl['inter_invoice'] !== null) {
                            $upslabel->setInternationalInvoice(1);
                        }

                        $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                        $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                        if ($upslabel->save() !== false && $this->_conf->getStoreConfig('upslabel/printing/automatic_printing', $this->storeId) == 1 && $upsl_one['type_print'] != "GIF") {
                            $toPrintData[] = $upsl_one['graphicImage'];
                        }

                        if ($this->shipmentId === null && $this->type != "refund") {
                            if (empty($this->shipment) && $this->order->canShip() && count($this->order->getShipmentsCollection()) == 0) {
                                if ($this->_registry->registry('current_shipment')) {
                                    $this->_registry->unregister('current_shipment');
                                }

                                $convertOrder = $this->convertOrder->create();
                                $shipment = $convertOrder->toShipment($this->order);

                                foreach ($this->order->getAllItems() as $orderItem) {
                                    // Check if order item has qty to ship or is virtual
                                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                                        continue;
                                    }

                                    $qtyShipped = $orderItem->getQtyToShip();
                                    $this->_conf->log("colichestvo");
                                    $this->_conf->log($qtyShipped);

                                    // Create shipment item with qty
                                    $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                                    // Add shipment item to shipment
                                    $shipment->addItem($shipmentItem);
                                }

                                if ($shipment) {
                                    $shipment->register();
                                    $shipment->getOrder()->setIsInProcess(true);
                                    $this->shipmentRepository->save($shipment);
                                    $this->orderRepository->save($shipment->getOrder());
                                    /*$transactionSave = $this->objectManager->create(
                                        'Magento\Framework\DB\Transaction'
                                    );
                                    $transactionSave->addObject(
                                        $shipment
                                    )->addObject(
                                        $shipment->getOrder()
                                    );
                                    $transactionSave->save();*/
                                }

                                $this->shipment = $this->shipmentRepository->get($shipment->getId());
                                $this->shipmentId = $this->shipment->getId();

                                foreach ($this->shipment->getAllItems() as $_item) {
                                    if (!$_item->getOrderItem()->getParentItem()) {
                                    }
                                }
                            } else {
                                $this->shipment = $this->order->getShipmentsCollection()->getFirstItem();
                                $this->shipmentId = $this->shipment->getId();
                            }
                        }

                        $upslabel->setShipmentId($this->shipmentId);
                        if ($this->type != "refund") {
                            $upslabel->setShipmentIncrementId($this->shipment->getIncrementId());
                        }

                        $upslabel->save();
                        if ($this->_conf->getStoreConfig('upslabel/additional_settings/orderstatuses', $this->storeId) != '') {
                            $this->order->setStatus($this->_conf->getStoreConfig('upslabel/additional_settings/orderstatuses', $this->storeId), true);
                            $this->order->save();
                        }

                        $this->label[] = $upslabel;
                    }

                    $path = $this->_conf->getBaseDir('media') . '/upslabel/inter_pdf/';
                    if (!is_dir($path)) {
                        mkdir($path, 0777);
                    }

                    if (isset($upsl['inter_invoice']) && $upsl['inter_invoice'] !== null) {
                        file_put_contents($path . $upsl['shipidnumber'] . ".pdf", base64_decode($upsl['inter_invoice']));
                    }

                    $pathTurnInPage = $this->_conf->getBaseDir('media') . '/upslabel/turn_in_page/';
                    if (!is_dir($pathTurnInPage)) {
                        mkdir($pathTurnInPage, 0777);
                    }

                    if (isset($upsl['turn_in_page']) && $upsl['turn_in_page'] !== null) {
                        file_put_contents($pathTurnInPage . $upsl['shipidnumber'] . ".html", base64_decode($upsl['turn_in_page']));
                    }

                    if (isset($params['default_return']) && $params['default_return'] == 1 && $this->type != "refund") {
                        $upsl3 = $upsl2;
                        $upsl2 = null;
                        foreach ($upsl3 as $upsl2) {
                            if (isset($upsl2) && !empty($upsl2) && (!array_key_exists('error', $upsl2) || !$upsl2['error'])) {
                                foreach ($upsl2['arrResponsXML'] as $upsl_one) {
                                    $upslabel = $this->labelFactory->create();
                                    $upslabel->setTitle('Order ' . $this->order->getIncrementId() . ' TN' . $upsl_one['trackingnumber']);
                                    $upslabel->setOrderId($this->order->getId());
                                    $upslabel->setOrderIncrementId($this->order->getIncrementId());
                                    $upslabel->setShipmentId($this->shipmentId);
                                    if ($this->type == "shipment") {
                                        $upslabel->setShipmentIncrementId($this->shipment->getIncrementId());
                                    }

                                    $upslabel->setType($this->type);
                                    $upslabel->setType2('refund');
                                    $upslabel->setTrackingnumber($upsl_one['trackingnumber']);
                                    $upslabel->setShipmentidentificationnumber($upsl['shipidnumber']);
                                    $upslabel->setShipmentidentificationnumber2($upsl2['shipidnumber']);
                                    $upslabel->setShipmentdigest($upsl2['digest']);
                                    if ($upsl_one['type_print'] !== 'link') {
                                        $upslabel->setLabelname('label' . $upsl_one['trackingnumber'] . '.' . strtolower($upsl_one['type_print']));
                                    } else {
                                        $upslabel->setLabelname($upsl_one['graphicImage']);
                                    }

                                    $upslabel->setStatustext(__('Successfully'));
                                    $upslabel->setTypePrint($upsl_one['type_print']);
                                    $upslabel->setLstatus(0);
                                    $upslabel->setPrice($upsl2['price']['price']);
                                    $upslabel->setCurrency($upsl2['price']['currency']);
                                    $upslabel->setStoreId($this->order->getStoreId());
                                    $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                                    $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));

                                    if ($upslabel->save() !== false && $upsl_one['type_print'] !== 'link' && $this->_conf->getStoreConfig('upslabel/printing/automatic_printing', $this->storeId) == 1 && $upsl_one['type_print'] != "GIF" && $upsl_one['type_print'] != "PDF" && $upsl_one['type_print'] != 'virtual') {
                                        $toPrintData[] = $upsl_one['graphicImage'];
                                    }

                                    $this->label2[] = $upslabel;
                                }

                                if (isset($params['addtrack']) && $params['addtrack'] == 1 && $this->type == 'shipment') {
                                    $trTitle = 'United Parcel Service (return)';
                                    if ($this->shipment) {
                                        foreach ($upsl2['arrResponsXML'] as $upsl_one1) {
                                            $track = $this->objectManager->create('Magento\Sales\Model\Order\Shipment\Track')
                                                ->setNumber(trim($upsl_one1['trackingnumber']))
                                                ->setCarrierCode('ups')
                                                ->setTitle($trTitle);
                                            $this->shipment->addTrack($track);
                                        }
                                    }
                                }
                            } else {
                                $upslabel = $this->labelFactory->create();
                                $upslabel->setTitle('Order ' . $this->order->getIncrementId());
                                $upslabel->setOrderId($this->order->getId());
                                $upslabel->setOrderIncrementId($this->order->getIncrementId());
                                $upslabel->setShipmentId($this->shipmentId);
                                $upslabel->setType($this->type);
                                $upslabel->setType2('refund');
                                $upslabel->setStatustext($upsl2['error']);
                                if (isset($upsl2['request']) && isset($upsl2['response'])) {
                                    $upslabel->setXmllog($upsl2['request'] . $upsl2['response']);
                                }

                                $upslabel->setLstatus(1);
                                $upslabel->setStoreId($this->order->getStoreId());
                                $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                                $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                                $upslabel->save();
                                $this->label2[] = $upslabel;
                            }
                        }
                    }

                    if (isset($params['addtrack']) && $params['addtrack'] == 1 && $this->type != 'refund') {
                        $trTitle = 'United Parcel Service';
                        if ($this->shipment) {
                            foreach ($upsl['arrResponsXML'] as $upsl_one1) {
                                $track = $this->objectManager->create('Magento\Sales\Model\Order\Shipment\Track')
                                    ->setNumber(trim($upsl_one1['trackingnumber']))
                                    ->setCarrierCode('ups')
                                    ->setTitle($trTitle);
                                $this->shipment->addTrack($track);
                            }
                            $this->shipmentRepository->save($this->shipment);
                            if ($this->_conf->getStoreConfig('upslabel/shipping/track_send', $this->storeId) == 1) {
                                $this->objectManager->create('Magento\Sales\Model\Service\ShipmentService')
                                    ->notify($this->shipment->getId());
                            }
                        }
                    }

                    if (count($toPrintData) > 0) {
                        $this->_conf->sendPrint($toPrintData, $this->storeId);
                    }
                } else {
                    $upslabel = $this->labelFactory->create();
                    $upslabel->setTitle('Order ' . $this->order->getIncrementId());
                    $upslabel->setOrderId($this->order->getId());
                    $upslabel->setOrderIncrementId($this->order->getIncrementId());
                    $upslabel->setShipmentId($this->shipmentId);
                    $upslabel->setType($this->type);
                    $upslabel->setType2($this->type);
                    $upslabel->setStatustext($upsl['error']);
                    if (isset($upsl['request']) && isset($upsl['response'])) {
                        $upslabel->setXmllog($upsl['request'] . $upsl['response']);
                    }

                    $upslabel->setLstatus(1);
                    $upslabel->setStoreId($this->order->getStoreId());
                    $upslabel->setCreatedTime(Date("Y-m-d H:i:s"));
                    $upslabel->setUpdateTime(Date("Y-m-d H:i:s"));
                    $upslabel->save();
                    $this->label[] = $upslabel;
                }
            }
            return true;
        }
        return false;
    }

    public function deleteLabel($shipidnumber = null, $type = 'shipidnumber')
    {
        $labels = null;
        if ($shipidnumber !== null) {
            if ($type == 'shipidnumber') {
                $labels = $this->labelsModel->getCollection()->addFieldToFilter('shipmentidentificationnumber', ['in' => $shipidnumber]);
            } elseif ($type == 'label_ids') {
                $labels = $this->labelsModel->getCollection()->addFieldToFilter('upslabel_id', ['in' => $shipidnumber]);
            }

            if ($labels !== null && count($labels) > 0) {
                $this->order = null;
                $this->shipmentId = null;
                foreach ($labels as $model) {
                    if ($this->order === null) {
                        $this->order = $this->orderRepository->get($model->getOrderId());
                    }

                    if ($model->getShipmentId() > 0 && $this->shipmentId === null) {
                        $this->shipmentId = $model->getShipmentId();
                    }

                    $this->type = $model->getType();
                    $this->storeId = $this->order->getStoreId();
                    if ($model->getLstatus() == 0) {
                        $lbl = $this->upsFactory->create();

                        $lbl->packagingReferenceNumberCode = $this->_conf->getStoreConfig('upslabel/packaging/packagingreferencenumbercode', $this->storeId);
                        $lbl->testing = $this->_conf->getStoreConfig('upslabel/testmode/testing', $this->storeId);
                        $lbl->storeId = $this->storeId;
                        $this->intermediate($this->order, $model->getType(), $this->shipmentId);
                        $this->setParams($lbl, $this->defConfParams, $this->defPackageParams);
                        $result = $lbl->deleteLabel($model->getShipmentidentificationnumber2());
                        if ($result !== null) {
                            $this->messageManager->addErrorMessage('Delete UPS label. Tracking Number: ' . $model->getTrackingnumber() . '. Error: ' . $result['error']);
                        }

                        if (file_exists($this->_conf->getBaseDir('media') . '/upslabel/label/' . $model->getLabelname())) {
                            unlink($this->_conf->getBaseDir('media') . '/upslabel/label/' . $model->getLabelname());
                        }

                        if (file_exists($this->_conf->getBaseDir('media') . '/upslabel/label/' . $model->getTrackingnumber() . 'pdf417.gif')) {
                            unlink($this->_conf->getBaseDir('media') . '/upslabel/label/' . $model->getTrackingnumber() . 'pdf417.gif');
                        }

                        if (file_exists($this->_conf->getBaseDir('media') . '/upslabel/label/' . $model->getTrackingnumber() . '.html')) {
                            unlink($this->_conf->getBaseDir('media') . '/upslabel/label/' . $model->getTrackingnumber() . '.html');
                        }

                        if (file_exists($this->_conf->getBaseDir('media') . '/upslabel/label/' . "HVR" . $model->getShipmentidentificationnumber2() . ".html")) {
                            unlink($this->_conf->getBaseDir('media') . '/upslabel/label/' . "HVR" . $model->getShipmentidentificationnumber2() . ".html");
                        }

                        if (file_exists($this->_conf->getBaseDir('media') . '/upslabel/inter_pdf/' . $model->getTrackingnumber() . ".pdf")) {
                            unlink($this->_conf->getBaseDir('media') . '/upslabel/inter_pdf/' . $model->getTrackingnumber() . ".pdf");
                        }

                        if ($model->getShipmentId() > 0) {
                            $shipm = $this->shipmentRepository->get($model->getShipmentId());
                            $tracks = $shipm->getAllTracks();
                            foreach ($tracks as $track) {
                                if ($track->getNumber() == $model->getTrackingnumber()) {
                                    $track->delete();
                                }
                            }
                        }
                    }

                    $model->delete();
                }

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setPickup($data)
    {
        $this->storeId = isset($data['store']) ? $data['store'] : null;
        $this->pickup = $this->upsFactory->create();
        $this->pickup->_handy = $this;
        $this->pickup->accessLicenseNumber = $this->_conf->getStoreConfig('upslabel/credentials/accesslicensenumber', $this->storeId);
        $this->pickup->userId = $this->_conf->getStoreConfig('upslabel/credentials/userid', $this->storeId);
        $this->pickup->password = $this->_conf->getStoreConfig('upslabel/credentials/password', $this->storeId);
        $this->pickup->shipperNumber = $this->_conf->getStoreConfig('upslabel/credentials/shippernumber', $this->storeId);

        $address = $this->addresses->getAddressesById($this->_conf->getStoreConfig('upslabel/shipping/defaultshipper', $this->storeId));

        $this->pickup->ratePickupIndicator = "N"/*$data['RatePickupIndicator']*/
        ;
        $this->pickup->shipperCountryCode = $address->getCountry();
        $this->pickup->closeTime = $data['CloseTime'];
        $this->pickup->readyTime = $data['ReadyTime'];
        $this->pickup->pickupDateYear = $data['PickupDateYear'];
        $this->pickup->pickupDateMonth = $data['PickupDateMonth'];
        $this->pickup->pickupDateDay = $data['PickupDateDay'];
        if (isset($data['oadress']['OtherAddress']) && $data['oadress']['OtherAddress'] == 1) {
            $this->pickup->shipfromCompanyName = $this->_conf->escapeXML($data['oadress']['companyname']);
            $this->pickup->shipfromAttentionName = $this->_conf->escapeXML($data['oadress']['attentionname']);
            $this->pickup->shipfromAddressLine1 = $this->_conf->escapeXML($data['oadress']['addressline1']);
            $this->pickup->room = $this->_conf->escapeXML($data['oadress']['room']);
            $this->pickup->floor = $this->_conf->escapeXML($data['oadress']['floor']);
            $this->pickup->shipfromCity = $this->_conf->escapeXML($data['oadress']['city']);
            $this->pickup->shipfromStateProvinceCode = $this->_conf->escapeXML($data['oadress']['stateprovincecode']);
            $this->pickup->urbanization = $this->_conf->escapeXML($data['oadress']['urbanization']);
            $this->pickup->shipfromPostalCode = $this->_conf->escapeXML($data['oadress']['postalcode']);
            $this->pickup->shipfromCountryCode = $this->_conf->escapeXML($data['oadress']['countrycode']);
            $this->pickup->residential = $this->_conf->escapeXML($data['oadress']['residential']);
            $this->pickup->pickupPoint = $this->_conf->escapeXML($data['oadress']['pickup_point']);
            $this->pickup->shipfromPhoneNumber = $this->_conf->escapeXML($data['oadress']['phonenumber']);
        } else {
            $pickupAddress = $this->addresses->getAddressesById($data['ShipFrom']);
            $this->pickup->shipfromCompanyName = $this->_conf->escapeXML($pickupAddress->getCompany());
            $this->pickup->shipfromAttentionName = $this->_conf->escapeXML($pickupAddress->getAttention());
            $this->pickup->shipfromAddressLine1 = $this->_conf->escapeXML($pickupAddress->getStreetOne());
            $this->pickup->room = $this->_conf->escapeXML($pickupAddress->getRoom());
            $this->pickup->floor = $this->_conf->escapeXML($pickupAddress->getFloor());
            $this->pickup->shipfromCity = $this->_conf->escapeXML($pickupAddress->getCity());
            $this->pickup->shipfromStateProvinceCode = $this->_conf->escapeXML($pickupAddress->getProvinceCode());
            $this->pickup->urbanization = $this->_conf->escapeXML($pickupAddress->getUrbanization());
            $this->pickup->shipfromPostalCode = $this->_conf->escapeXML($pickupAddress->getPostalCode());
            $this->pickup->shipfromCountryCode = $this->_conf->escapeXML($pickupAddress->getCountry());
            $this->pickup->residential = $this->_conf->escapeXML($pickupAddress->getResidential());
            $this->pickup->pickupPoint = $this->_conf->escapeXML($pickupAddress->getPickupPoint());
            $this->pickup->shipfromPhoneNumber = $this->_conf->escapeXML($pickupAddress->getPhone());
        }

        $this->pickup->alternateAddressIndicator = $data['AlternateAddressIndicator'];
        $this->pickup->serviceCode = $data['ServiceCode'];
        $this->pickup->quantity = $data['Quantity'];
        $this->pickup->destinationCountryCode = $data['DestinationCountryCode'];
        $this->pickup->containerCode = $data['ContainerCode'];
        $this->pickup->weight = $data['Weight'];
        $this->pickup->unitOfMeasurement = $data['UnitOfMeasurement'];
        $this->pickup->overweightIndicator = $data['OverweightIndicator'];
        $this->pickup->paymentMethod = $data['PaymentMethod'];
        $this->pickup->specialInstruction = $data['SpecialInstruction'];
        $this->pickup->referenceNumber = $data['ReferenceNumber'];
        $this->pickup->notification = $data['Notification'];
        $this->pickup->confirmationEmailAddress = $data['ConfirmationEmailAddress'];
        $this->pickup->undeliverableEmailAddress = $data['UndeliverableEmailAddress'];
        $this->pickup->testing = $this->_conf->getStoreConfig('upslabel/testmode/testing', $this->storeId);
    }

    public function cancelPickup($id)
    {
        $model = $this->pickupModel->load($id);
        $data = $model->getData();
        $this->storeId = $model->getStore();
        if ($this->pickup === null) {
            $this->pickup = $this->upsFactory->create();
        }

        $this->pickup->_handy = $this;
        $this->pickup->accessLicenseNumber = $this->_conf->getStoreConfig('upslabel/credentials/accesslicensenumber', $this->storeId);
        $this->pickup->userId = $this->_conf->getStoreConfig('upslabel/credentials/userid', $this->storeId);
        $this->pickup->password = $this->_conf->getStoreConfig('upslabel/credentials/password', $this->storeId);
        $this->pickup->shipperNumber = $this->_conf->getStoreConfig('upslabel/credentials/shippernumber', $this->storeId);
        $this->pickup->testing = $this->_conf->getStoreConfig('upslabel/testmode/testing', $this->storeId);
        $xml = simplexml_load_string($data['pickup_response']);
        $soap = $xml->children('soapenv', true)->Body[0];
        $PRN = $soap->children('pkup', true)->PickupCreationResponse[0]->PRN;
        $this->pickup->cancelPickup($PRN);
    }

    public function macropaste($value)
    {
        return str_replace(
            ["#order_id#", "#customer_name#", "#sku#"],
            [$this->order->getIncrementId(), $this->shippingAddress->getFirstname() . ' ' . $this->shippingAddress->getLastname(), $this->sku],
            $value
        );
    }

    protected function _getBaseCurrencyKoef($from, $to)
    {
        return $this->_currencyFactory->create()->load(
            $from
        )->getAnyRate(
            $to
        );
    }
}