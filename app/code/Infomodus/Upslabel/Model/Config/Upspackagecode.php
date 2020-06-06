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

class Upspackagecode implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'UPS Letter (Envelope)', 'value' => '01'],
            ['label' => 'Customer Supplied Package', 'value' => '02'],
            ['label' => 'Tube', 'value' => '03'],
            ['label' => 'PAK', 'value' => '04'],
            ['label' => 'UPS Express Box', 'value' => '21'],
            ['label' => 'UPS 25KG Box', 'value' => '24'],
            ['label' => 'UPS 10KG Box', 'value' => '25'],
            ['label' => 'Pallet', 'value' => '30'],
            ['label' => 'Small Express Box', 'value' => '2a'],
            ['label' => 'Medium Express Box', 'value' => '2b'],
            ['label' => 'Large Express Box', 'value' => '2c'],
        ];
    }

    public function getPackagingtypecode()
    {
        return [
            '01' => 'UPS Letter (Envelope)',
            '02' => 'Customer Supplied Package',
            '03' => 'Tube',
            '04' => 'PAK',
            '21' => 'UPS Express Box',
            '24' => 'UPS 25KG Box',
            '25' => 'UPS 10KG Box',
            '30' => 'Pallet',
            '2a' => 'Small Express Box',
            '2b' => 'Medium Express Box',
            '2c' => 'Large Express Box',
        ];
    }
}
