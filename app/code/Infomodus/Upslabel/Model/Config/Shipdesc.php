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

class Shipdesc implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Customer name + Order Id'), 'value' => '1'],
            ['label' => __('Only Customer name'), 'value' => '2'],
            ['label' => __('Only Order Id'), 'value' => '3'],
            ['label' => __('Custom value'), 'value' => '4'],
            ['label' => __('nothing'), 'value' => ''],
        ];
    }
}
