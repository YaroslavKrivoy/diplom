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

class Containercode implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('PACKAGE'), 'value' => '01'],
            ['label' => __('UPS LETTER'), 'value' => '02'],
            ['label' => __('PALLET'), 'value' => '03'],
        ];
    }
}
