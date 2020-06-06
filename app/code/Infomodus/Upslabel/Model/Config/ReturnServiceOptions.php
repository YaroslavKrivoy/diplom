<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class ReturnServiceOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $c = [
            /*['label' => 'UPS Print and Mail (PNM)', 'value' => '2'],
            ['label' => 'UPS Return Service 1-Attempt (RS1)', 'value' => '3'],
            ['label' => 'UPS Return Service', 'value' => '5'],*/
            /*['label' => 'Attempt (RS3)', 'value' => '3'], NOT USED*/
            ['label' => 'UPS Electronic Return Label (ERL)', 'value' => '8'],
            ['label' => 'UPS Print Return Label (PRL)', 'value' => '9'],
            /*['label' => 'UPS Exchange Print Return Label', 'value' => '10'],
            ['label' => 'UPS Pack & Collect Service 1-Attempt Box 1', 'value' => '11'],
            ['label' => 'UPS Pack & Collect Service 1-Attempt Box 2', 'value' => '12'],
            ['label' => 'UPS Pack & Collect Service 1-Attempt Box 3', 'value' => '13'],
            ['label' => 'UPS Pack & Collect Service 1-Attempt Box 4', 'value' => '14'],
            ['label' => 'UPS Pack & Collect Service 1-Attempt Box 5', 'value' => '15'],
            ['label' => 'UPS Pack & Collect Service 3-Attempt Box 1', 'value' => '16'],
            ['label' => 'UPS Pack & Collect Service 3-Attempt Box 2', 'value' => '17'],
            ['label' => 'UPS Pack & Collect Service 3-Attempt Box 3', 'value' => '18'],
            ['label' => 'UPS Pack & Collect Service 3-Attempt Box 4', 'value' => '19'],
            ['label' => 'UPS Pack & Collect Service 3-Attempt Box 5', 'value' => '20'],*/
        ];
        return $c;
    }
}
