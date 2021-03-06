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

class PrintStatus implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __("Unprinted"), 'value' => 0],
            ['label' => __("Printed"), 'value' => 1],
        ];
    }
}
