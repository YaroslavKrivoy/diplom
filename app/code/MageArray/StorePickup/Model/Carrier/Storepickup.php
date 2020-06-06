<?php
namespace MageArray\StorePickup\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Storepickup extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'storepickup';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \MageArray\StorePickup\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['storepickup' => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $enable = $this->dataHelper->isEnabled();
        $methodproductWise = $this->dataHelper->showMethodProductWise();
        if ($enable == 1 && $methodproductWise == 1) {
            if ($request->getAllItems()) {
                $flag = false;
                foreach ($request->getAllItems() as $item) {
                    if ($item->getProduct()->getAvailableAtStore()) {
                        $flag = true;
                    }
                }
                if ($flag == false) {
                    return false;
                }
            }
        }

        if ($enable == 1 &&
            $request->getBaseSubtotalInclTax() >= $this->getConfigData('min_shipping_subtotal')
        ) {
            /** @var \Magento\Shipping\Model\Rate\Result $result */
            $result = $this->_rateResultFactory->create();

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('storepickup');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('storepickup');
            $method->setMethodTitle($this->getConfigData('name'));

            $amount = $this->getConfigData('price');

            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);

            return $result;
        }
        return false;
    }
}
