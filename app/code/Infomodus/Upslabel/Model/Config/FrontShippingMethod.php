<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config;

class FrontShippingMethod implements OptionSourceInterface
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function toOptionArray($isMultiSelect = false)
    {
        $option = [['value'=>'', 'label'=> __('--Please Select--')]];
        $_methods = $this->config->getActiveCarriers();
        foreach ($_methods as $_carrierCode => $_carrier) {
            if(/*$_carrierCode !=="ups" && */$_carrierCode !=="dhlint" && $_carrierCode !=="usps" && $_method = $_carrier->getAllowedMethods())  {
                /*if(!$_title = $this->getStoreConfig('carriers/'.$_carrierCode.'/title', $storeId)) {*/
                    $_title = $_carrierCode;
                /*}*/
                foreach ($_method as $_mcode => $_m) {
                    if (is_array($_m)) {
                        foreach ($_m as $_mcode2 => $_m2) {
                            $_code = $_carrierCode . '_' . $_mcode2;
                            $option[] = ['label' => ("(".$_title.")  ". $_m2), 'value' => $_code];
                        }
                    } else {
                        $_code = $_carrierCode . '_' . $_mcode;
                        $option[] = ['label' => ("(".$_title.")  ". $_m), 'value' => $_code];
                    }
                }
            }
        }

        return $option;
    }
}
