<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config\Pickup;

use Magento\Framework\Data\OptionSourceInterface;

class Servicecode implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Choose', 'value' => ""],
            ['label' => 'UPS Next Day Air', 'value' => '001'],
            ['label' => 'UPS 2nd Day Air', 'value' => '002'],
            ['label' => 'UPS Ground', 'value' => '003'],
            ['label' => 'UPS Ground, UPS Standard', 'value' => '004'],
            ['label' => 'UPS Worldwide Express', 'value' => '007'],
            ['label' => 'UPS Worldwide Expedited', 'value' => '008'],
            ['label' => 'UPS Standard', 'value' => '011'],
            ['label' => 'UPS Three Day Select', 'value' => '012'],
            ['label' => 'UPS Next Day Air Saver', 'value' => '013'],
            ['label' => 'UPS Next Day Air Early A.M.', 'value' => '014'],
            ['label' => 'UPS Economy', 'value' => '021'],
            ['label' => 'UPS Basic', 'value' => '031'],
            ['label' => 'UPS Worldwide Express Plus', 'value' => '054'],
            ['label' => 'UPS Second Day Air A.M.', 'value' => '059'],
            ['label' => 'UPS Express NA1', 'value' => '064'],
            ['label' => 'UPS Saver', 'value' => '065'],
            ['label' => 'UPS Standard Today', 'value' => '082'],
            ['label' => 'UPS Today Dedicated Courier', 'value' => '083'],
            ['label' => 'UPS Intercity Today', 'value' => '084'],
            ['label' => 'UPS Today Express', 'value' => '085'],
            ['label' => 'UPS Today Express Saver', 'value' => '086'],
            ['label' => 'UPS Worldwide Express Freight', 'value' => '096'],
        ];
    }
}
