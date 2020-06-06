<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 06.02.12
 * Time: 16:17
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Directory\Model\Country;
use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    public $country;

    public function __construct(
        Country $country
    ) {
        $this->country = $country;
    }

    public function toOptionArray(){
        return [];
    }

    public function getProvinceCode($code, $countryCode = null)
    {
        $provinceCodes = [];
        $provinceCodes['US'] = [
            'Alabama' => 'AL',
            'Alaska' => 'AK',
            'American Samoa' => 'AS',
            'Arizona' => 'AZ',
            'Arkansas' => 'AR',
            'Armed Forces Africa' => 'AE',
            'Armed Forces Americas' => 'AA',
            'Armed Forces Canada' => 'AE',
            'Armed Forces Europe' => 'AE',
            'Armed Forces Middle East' => 'AE',
            'Armed Forces Pacific' => 'AP',
            'California' => 'CA',
            'Colorado' => 'CO',
            'Connecticut' => 'CT',
            'Delaware' => 'DE',
            'District of Columbia' => 'DC',
            'Federated States Of Micronesia' => 'FM',
            'Florida' => 'FL',
            'Georgia' => 'GA',
            'Guam' => 'GU',
            'Hawaii' => 'HI',
            'Idaho' => 'ID',
            'Illinois' => 'IL',
            'Indiana' => 'IN',
            'Iowa' => 'IA',
            'Kansas' => 'KS',
            'Kentucky' => 'KY',
            'Louisiana' => 'LA',
            'Maine' => 'ME',
            'Marshall Islands' => 'MH',
            'Maryland' => 'MD',
            'Massachusetts' => 'MA',
            'Michigan' => 'MI',
            'Minnesota' => 'MN',
            'Mississippi' => 'MS',
            'Missouri' => 'MO',
            'Montana' => 'MT',
            'Nebraska' => 'NE',
            'Nevada' => 'NV',
            'New Hampshire' => 'NH',
            'New Jersey' => 'NJ',
            'New Mexico' => 'NM',
            'New York' => 'NY',
            'North Carolina' => 'NC',
            'North Dakota' => 'ND',
            'Northern Mariana Islands' => 'MP',
            'Ohio' => 'OH',
            'Oklahoma' => 'OK',
            'Oregon' => 'OR',
            'Palau' => 'PW',
            'Pennsylvania' => 'PA',
            'Puerto Rico' => 'PR',
            'Rhode Island' => 'RI',
            'South Carolina' => 'SC',
            'South Dakota' => 'SD',
            'Tennessee' => 'TN',
            'Texas' => 'TX',
            'Utah' => 'UT',
            'Vermont' => 'VT',
            'Virgin Islands' => 'VI',
            'Virginia' => 'VA',
            'Washington' => 'WA',
            'West Virginia' => 'WV',
            'Wisconsin' => 'WI',
            'Wyoming' => 'WY',
        ];
        $provinceCodes['CA'] = [
            'Alberta' => 'AB',
            'British Columbia' => 'BC',
            'Manitoba' => 'MB',
            'New Brunswick' => 'NB',
            'Newfoundland and Labrador' => 'NL',
            'Northwest Territories' => 'NT',
            'Nova Scotia' => 'NS',
            'Nunavut' => 'NU',
            'Ontario' => 'ON',
            'Prince Edward Island' => 'PE',
            'Quebec' => 'QC',
            'Saskatchewan' => 'SK',
            'Yukon' => 'YT',
            ];
        $provinceCodes['IE'] = [
            'Carlow' => 'IE-CW',
            'Cavan' => 'IE-CN',
            'Clare' => 'IE-CE',
            'Cork' => 'IE-CO',
            'Donegal' => 'IE-DL',
            'Dublin' => 'IE-D',
            'Galway' => 'IE-G',
            'Kerry' => 'IE-KY',
            'Kildare' => 'IE-KE',
            'Kilkenny' => 'IE-KK',
            'Laois' => 'IE-LS',
            'Leitrim' => 'IE-LM',
            'Limerick' => 'IE-LK',
            'Longford' => 'IE-LD',
            'Louth' => 'IE-LH',
            'Mayo' => 'IE-MO',
            'Meath' => 'IE-MH',
            'Monaghan' => 'IE-MN',
            'Offaly' => 'IE-OY',
            'Roscommon' => 'IE-RN',
            'Sligo' => 'IE-SO',
            'Tipperary' => 'IE-TA',
            'Waterford' => 'IE-WD',
            'Westmeath' => 'IE-WH',
            'Wexford' => 'IE-WX',
            'Wicklow' => 'IE-WW',
            'Connacht' => 'IE-C',
            'Leinster' => 'IE-L',
            'Munster' => 'IE-M',
            'Ulster' => 'IE-U',
        ];

        $stateCode = "";

        if ($countryCode !== null) {
            if ($countryCode != 'US' && $countryCode != 'CA' && $countryCode != 'IE') {
                return "";
            }

            if (isset($provinceCodes[$countryCode][$code])) {
                return $provinceCodes[$countryCode][$code];
            }

            $states = $this->country->loadByCode($countryCode)->getRegions()->getData();
            if (count($states) > 0) {
                foreach ($states as $state) {
                    if ($state['default_name'] == $code) {
                        $stateCode = $state['code'];
                    }
                }

                foreach ($states as $state) {
                    if ($state['code'] == $code) {
                        $stateCode = $state['code'];
                    }
                }
            }
        }

        return $stateCode;
    }

    public function getUnitOfMeasurement()
    {
        return [
            'IN' => 'Inches',
            'CM' => 'Centimeters',
        ];
    }
}
