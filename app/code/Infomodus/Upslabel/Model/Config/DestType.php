<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class DestType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Auto', 'value' => 0],
            ['label' => 'By Company Field', 'value' => 3],
            ['label' => 'Residential', 'value' => 1],
            ['label' => 'Commercial', 'value' => 2],
        ];
    }
}
