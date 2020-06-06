<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */

namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\ScopeInterface;

class ShippingMethods implements OptionSourceInterface
{
    protected $scopeConfig;
    protected $shippingConfig;

    /**
     * ShippingMethods constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $shippingConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $shippingConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->shippingConfig = $shippingConfig;
    }

    public function toOptionArray($store = null)
    {
        $option = [];
        $_methods = $this->shippingConfig->getActiveCarriers($store);
        foreach ($_methods as $_carrierCode => $_carrier) {
            if ($_carrierCode !== "ups"
                && $_carrierCode !== "dhlint"
                && $_carrierCode !== "usps"
                && $_method = $_carrier->getAllowedMethods()
            ) {
                if (!$_title = $this->scopeConfig->getValue('carriers/' . $_carrierCode . '/title', ScopeInterface::SCOPE_STORE, $store)) {
                    $_title = $_carrierCode;
                }

                foreach ($_method as $_mcode => $_m) {
                    $_code = $_carrierCode . '_' . $_mcode;
                    $option[] = ['label' => "(" . $_title . ")  " . $_m, 'value' => $_code];
                }
            }
        }

        return $option;
    }

    public function getShippingMethodsSimple($store = null)
    {
        $option = [];
        $_methods = $this->shippingConfig->getActiveCarriers($store);
        foreach ($_methods as $_carrierCode => $_carrier) {
            if ($_carrierCode !== "ups"
                && $_carrierCode !== "dhlint"
                && $_carrierCode !== "usps"
                && $_method = $_carrier->getAllowedMethods()
            ) {
                if (!$_title = $this->scopeConfig->getValue('carriers/' . $_carrierCode . '/title', ScopeInterface::SCOPE_STORE, $store)) {
                    $_title = $_carrierCode;
                }

                foreach ($_method as $_mcode => $_m) {
                    $_code = $_carrierCode . '_' . $_mcode;
                    $option[$_code] = "(" . $_title . ")  " . $_m;
                }
            }
        }

        return $option;
    }
}
