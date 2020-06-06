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

class Paymentmethod implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Choose', 'value' => ""],
            ['label' => __('No payment needed'), 'value' => '00'],
            ['label' => __('Pay by shipper account'), 'value' => '01'],
            ['label' => __('Pay by charge card'), 'value' => '03'],
            ['label' => __('Pay by tracking number'), 'value' => '04'],
            ['label' => __('Pay by check or money order'), 'value' => '05'],
        ];
    }
}
