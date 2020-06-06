<?php
namespace Webfitters\Checkout\Model\Payment;

class Terms extends \Magento\Payment\Model\Method\AbstractMethod {

    protected $_code = 'webfitters_terms';
    protected $_isGateway = false;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_stripeApi = false;
    protected $_minAmount = null;
    protected $_maxAmount = null;
    protected $_supportedCurrencyCodes = array('USD');
    protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc'];

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Directory\Helper\Data $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
        $this->_minAmount = $this->getConfigData('min_order_total');
        $this->_maxAmount = $this->getConfigData('max_order_total');
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        $payment->setBaseAmountAuthorized($payment->getBaseAmountOrdered());
        $payment->setAmountAuthorized($payment->getAmountOrdered());
        $payment->save();
        return $this;
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        $payment->setBaseAmountPaid($payment->getBaseAmountAuthorized());
        $payment->setAmountPaid($payment->getAmountAuthorized());
        $payment->save();
        return $this;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        if (!$this->canRefund()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The refund action is not available.'));
        }
        return $this;
    }
    
}