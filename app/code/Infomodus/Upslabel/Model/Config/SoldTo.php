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

class SoldTo implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Customer'), 'value' => 'shipto'],
            ['label' => __('Shipper'), 'value' => 'shipper'],
        ];
    }
}
