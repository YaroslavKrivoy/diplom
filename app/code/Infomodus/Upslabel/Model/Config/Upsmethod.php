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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Upsmethod implements OptionSourceInterface
{
    protected $store;
    protected $request;
    protected $scopeConfig;

    /**
     * Upsmethod constructor.
     * @param StoreManagerInterface $store
     * @param RequestInterface $request
     */
    public function __construct(
        StoreManagerInterface $store,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->store = $store;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function toOptionArray()
    {
        $arr = $this->getUpsMethods();
        $c = array();
        foreach ($arr as $k => $v) {
            $c[] = array('value' => $k, 'label' => $v);
        }
        return $c;
    }

    public function getUpsMethods($storeCode = null)
    {
        $websiteCode = $this->request->getParam('website');
        $storeCode = $this->request->getParam('store', $storeCode);

        if (!$storeCode && $websiteCode) {
            $web = $this->store->getWebsite($websiteCode);
            $originShipment = $web->getConfig('upslabel/shipping/origin_shipment');
        } elseif ($storeCode) {
            $originShipment = $this->scopeConfig->getValue('upslabel/shipping/origin_shipment', ScopeInterface::SCOPE_STORE, $storeCode);
        } else {
            $originShipment = $this->scopeConfig->getValue('upslabel/shipping/origin_shipment');
        }

        if(!$originShipment){
            $originShipment = 'United States Domestic Shipments';
        }

        $c = array();
        foreach ($this->getCode('originShipment', $originShipment) as $k => $v) {
                $c[$k] = $v;
        }

        return $c;
    }

    public function getUpsMethodName($code = '', $storeCode = null)
    {
        $c = $this->getUpsMethods($storeCode);
        if (array_key_exists($code, $c)) {
            return $c[$code];
        } else {
            return false;
        }
    }

    public function getUpsMethodNumber($code = '')
    {
        $sercoD = [
            '1DM' => '14',
            '1DA' => '01',
            '1DP' => '13',
            '2DM' => '59',
            '2DA' => '02',
            '3DS' => '12',
            'GND' => '03',
            'EP' => '54',
            'XDM' => '54',
            'XPD' => '8',
            'XPR' => '7',
            'ES' => '07',
            'SV' => '65',
            'EX' => '08',
            'ST' => '11',
            'ND' => '07',
            'WXS' => '65',
        ];

        $sercoD2 = [
            '14' => '14',
            '1' => '01',
            '13' => '13',
            '59' => '59',
            '2' => '02',
            '12' => '12',
            '3' => '03',
            '54' => '54',
            '65' => '65',
            '8' => '08',
            '11' => '11',
            '7' => '07',
        ];
        $code = array_key_exists($code, $sercoD) ? $sercoD[$code] : $code;
        $code = array_key_exists($code, $sercoD2) ? $sercoD2[$code] : $code;

        return $code;
    }

    public function getCode($type, $code = '')
    {
        $codes = array(
            'action' => array(
                'single' => '3',
                'all' => '4',
            ),

            'originShipment' => array(
                // United States Domestic Shipments
                'United States Domestic Shipments' => array(
                    '01' => __('UPS Next Day Air'),
                    '02' => __('UPS 2nd Day Air'),
                    '03' => __('UPS Ground'),
                    /*'07' => __('UPS Worldwide Express'),
                    '08' => __('UPS Worldwide Expedited'),
                    '11' => __('UPS Standard'),*/
                    '12' => __('UPS 3 Day Select'),
                    '13' => __('UPS Next Day Air Saver'),
                    '14' => __('UPS Next Day Air Early'),
                    /*'54' => __('UPS Worldwide Express Plus'),*/
                    '59' => __('UPS 2nd Day Air A.M.'),
                    /*'65' => __('UPS Saver'),*/
                ),
                // Shipments Originating in United States
                'Shipments Originating in United States' => array(
                    '01' => __('UPS Next Day Air'),
                    '02' => __('UPS 2nd Day Air'),
                    '03' => __('UPS Ground'),
                    '07' => __('UPS Worldwide Express'),
                    '08' => __('UPS Worldwide Expedited'),
                    '11' => __('UPS Standard'),
                    '12' => __('UPS 3 Day Select'),
                    '14' => __('UPS Next Day Air Early'),
                    '54' => __('UPS Worldwide Express Plus'),
                    '59' => __('UPS 2nd Day Air A.M.'),
                    '65' => __('UPS Worldwide Saver'),
                ),
                // Shipments Originating in Canada
                'Shipments Originating in Canada' => array(
                    '01' => __('UPS Express'),
                    '02' => __('UPS 2nd Day Air'),
                    '07' => __('UPS Worldwide Express'),
                    '08' => __('UPS Worldwide Expedited'),
                    '11' => __('UPS Standard'),
                    '12' => __('UPS 3 Day Select'),
                    '13' => __('UPS Next Day Air Saver'),
                    '14' => __('UPS Express Early'),
                    '54' => __('UPS Worldwide Express Plus'),
                    '65' => __('UPS Express Saver'),
                    '70' => __('UPS Access Point Economy'),
                ),
                // Shipments Originating in the European Union
                'Shipments Originating in the European Union' => array(
                    '07' => __('UPS Express'),
                    '08' => __('UPS Expedited'),
                    '11' => __('UPS Standard'),
                    '54' => __('UPS Worldwide Express Plus'),
                    '65' => __('UPS Worldwide Saver'),
                    '70' => __('UPS Access Point Economy'),
                ),
                // Polish Domestic Shipments
                'Polish Domestic Shipments' => array(
                    '07' => __('UPS Express'),
                    '08' => __('UPS Expedited'),
                    '11' => __('UPS Standard'),
                    '54' => __('UPS Express Plus'),
                    '65' => __('UPS Express Saver'),
                    '70' => __('UPS Access Point Economy'),
                    '82' => __('UPS Today Standard'),
                    '83' => __('UPS Today Dedicated Courier'),
                    '85' => __('UPS Today Express'),
                    '86' => __('UPS Today Express Saver'),
                ),
                // Puerto Rico Origin
                'Puerto Rico Origin' => array(
                    '01' => __('UPS Next Day Air'),
                    '02' => __('UPS 2nd Day Air'),
                    '03' => __('UPS Ground'),
                    '07' => __('UPS Worldwide Express'),
                    '08' => __('UPS Worldwide Expedited'),
                    '14' => __('UPS Next Day Air Early'),
                    '54' => __('UPS Worldwide Express Plus'),
                    '65' => __('UPS Worldwide Saver'),
                ),
                // Shipments Originating in Mexico
                'Shipments Originating in Mexico' => array(
                    '07' => __('UPS Express'),
                    '08' => __('UPS Expedited'),
                    '11' => __('UPS Standard'),
                    '54' => __('UPS Express Plus'),
                    '65' => __('UPS Worldwide Saver'),
                    '70' => __('UPS Access Point Economy'),
                ),
                // Shipments Originating in Other Countries
                'Shipments Originating in Other Countries' => array(
                    '07' => __('UPS Express'),
                    '08' => __('UPS Worldwide Expedited'),
                    '11' => __('UPS Standard'),
                    '54' => __('UPS Worldwide Express Plus'),
                    '65' => __('UPS Worldwide Saver')
                )
            ),
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }
}
