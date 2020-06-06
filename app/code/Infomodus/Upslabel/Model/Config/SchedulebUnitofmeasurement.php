<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 07.02.12
 * Time: 10:49
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class SchedulebUnitofmeasurement implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $array = [
            ['value' => 'BBL', 'label' => 'Barrels',],
            ['value' => 'CAR', 'label' => 'Carat',],
            ['value' => 'CKG', 'label' => 'Content Kilogram',],
            ['value' => 'CM2', 'label' => 'Square Centimeters',],
            ['value' => 'CTN', 'label' => 'Content Ton',],
            ['value' => 'CUR', 'label' => 'Curie',],
            ['value' => 'CYK', 'label' => 'Clean Yield Kilogram',],
            ['value' => 'DOZ', 'label' => 'Dozen',],
            ['value' => 'DPC', 'label' => 'Dozen Pieces',],
            ['value' => 'DPR', 'label' => 'Dozen Pairs',],
            ['value' => 'FBM', 'label' => 'Fiber Meter',],
            ['value' => 'GCN', 'label' => 'Gross Containers',],
            ['value' => 'GM', 'label' => 'Gram',],
            ['value' => 'GRS', 'label' => 'Gross',],
            ['value' => 'HUN', 'label' => 'Hundred',],
            ['value' => 'KG', 'label' => 'Kilogram',],
            ['value' => 'KM3', 'label' => '1,000 Cubic Meters',],
            ['value' => 'KTS', 'label' => 'Kilogram Total Sugars',],
            ['value' => 'L', 'label' => 'Liter',],
            ['value' => 'M', 'label' => 'Meter',],
            ['value' => 'M2', 'label' => 'Square Meters',],
            ['value' => 'M3', 'label' => 'Cubic Meters',],
            ['value' => 'MC', 'label' => 'Millicurie',],
            ['value' => 'NO', 'label' => 'Number',],
            ['value' => 'PCS', 'label' => 'Pieces',],
            ['value' => 'PFL', 'label' => 'Proof Liter',],
            ['value' => 'PK', 'label' => 'Pack',],
            ['value' => 'PRS', 'label' => 'Pairs',],
            ['value' => 'RBA', 'label' => 'Running Bales',],
            ['value' => 'SQ', 'label' => 'Square',],
            ['value' => 'T', 'label' => 'Ton',],
            ['value' => 'THS', 'label' => '1,000',],
            ['value' => 'X', 'label' => 'No Quantity required',],
        ];
        return $array;
    }

    public function getScheduleUnitName($key)
    {
        $array = [
            'BBL' => 'Barrels',
            'CAR' => 'Carat',
            'CKG' => 'Content Kilogram',
            'CM2' => 'Square Centimeters',
            'CTN' => 'Content Ton',
            'CUR' => 'Curie',
            'CYK' => 'Clean Yield Kilogram',
            'DOZ' => 'Dozen',
            'DPC' => 'Dozen Pieces',
            'DPR' => 'Dozen Pairs',
            'FBM' => 'Fiber Meter',
            'GCN' => 'Gross Containers',
            'GM' => 'Gram',
            'GRS' => 'Gross',
            'HUN' => 'Hundred',
            'KG' => 'Kilogram',
            'KM3' => '1,000 Cubic Meters',
            'KTS' => 'Kilogram Total Sugars',
            'L' => 'Liter',
            'M' => 'Meter',
            'M2' => 'Square Meters',
            'M3' => 'Cubic Meters',
            'MC' => 'Millicurie',
            'NO' => 'Number',
            'PCS' => 'Pieces',
            'PFL' => 'Proof Liter',
            'PK' => 'Pack',
            'PRS' => 'Pairs',
            'RBA' => 'Running Bales',
            'SQ' => 'Square',
            'T' => 'Ton',
            'THS' => '1,000',
            'X' => 'No Quantity required',
        ];
        return isset($array[$key])?$array[$key]:'';
    }
}
