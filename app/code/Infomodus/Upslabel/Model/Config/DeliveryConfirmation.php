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

class DeliveryConfirmation implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Not used'), 'value' => 0],
            ['label' => __('Delivery Confirmation'), 'value' => 1],
            ['label' => __('Delivery Confirmation Signature Required'), 'value' => 2],
            ['label' => __('Delivery Confirmation Adult Signature Required'), 'value' => 3],
            ['label' => __('USPS Delivery Confirmation'), 'value' => 4],
        ];
    }

    public function getDeliveryConfirmation()
    {
        return [
            0 => __('Not used'),
            1 => __('Delivery Confirmation'),
            2 => __('Delivery Confirmation Signature Required'),
            3 => __('Delivery Confirmation Adult Signature Required'),
            4 => __('USPS Delivery Confirmation'),
        ];
    }
}
