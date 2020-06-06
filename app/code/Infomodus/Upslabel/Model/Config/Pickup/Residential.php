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

class Residential implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Non-residental (Commercial) address'), 'value' => 'N'],
            ['label' => __('Residential address'), 'value' => 'Y'],
        ];
    }
}
