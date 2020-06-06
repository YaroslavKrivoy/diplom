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

class InternationalUnitofmeasurement implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'BA', 'label' => 'Barrel'],
            ['value' => 'BE', 'label' => 'Bundle'],
            ['value' => 'BG', 'label' => 'Bag'],
            ['value' => 'BH', 'label' => 'Bunch'],
            ['value' => 'BOX', 'label' => 'Box'],
            ['value' => 'BT', 'label' => 'Bolt'],
            ['value' => 'BU', 'label' => 'Butt'],
            ['value' => 'CM', 'label' => 'Centimeter'],
            ['value' => 'CI', 'label' => 'Canister'],
            ['value' => 'CON', 'label' => 'Container'],
            ['value' => 'CR', 'label' => 'Crate'],
            ['value' => 'CS', 'label' => 'Case'],
            ['value' => 'CT', 'label' => 'Carton'],
            ['value' => 'CY', 'label' => 'Cylinder'],
            ['value' => 'DOZ', 'label' => 'Dozen'],
            ['value' => 'EA', 'label' => 'Each'],
            ['value' => 'EN', 'label' => 'Envelope'],
            ['value' => 'FT', 'label' => 'Feet'],
            ['value' => 'KG', 'label' => 'Kilogram'],
            ['value' => 'KGS', 'label' => 'Kilograms'],
            ['value' => 'LB', 'label' => 'Pound'],
            ['value' => 'LBS', 'label' => 'Pounds'],
            ['value' => 'L', 'label' => 'Liter'],
            ['value' => 'M', 'label' => 'Meter'],
            ['value' => 'NMB', 'label' => 'Number'],
            ['value' => 'PA', 'label' => 'Packet'],
            ['value' => 'PAL', 'label' => 'Pallet'],
            ['value' => 'PC', 'label' => 'Piece'],
            ['value' => 'PCS', 'label' => 'Pieces'],
            ['value' => 'PF', 'label' => 'Proof Liters'],
            ['value' => 'PKG', 'label' => 'Package'],
            ['value' => 'PR', 'label' => 'Pair'],
            ['value' => 'PRS', 'label' => 'Pairs'],
            ['value' => 'RL', 'label' => 'Roll'],
            ['value' => 'SET', 'label' => 'Set'],
            ['value' => 'SME', 'label' => 'Square Meters'],
            ['value' => 'SYD', 'label' => 'Square Yards'],
            ['value' => 'TU', 'label' => 'Tube'],
            ['value' => 'YD', 'label' => 'Yard'],
            ['value' => 'OTH', 'label' => 'Other'],
        ];
    }
}
