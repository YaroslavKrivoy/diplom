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

class Notificationcode implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Return Notification or Label Creation Notification', 'value' => '2'],
            ['label' => 'QV In-transit Notification', 'value' => '5'],
            ['label' => 'QV Ship Notification', 'value' => '6'],
            ['label' => 'QV Exception Notification', 'value' => '7'],
            ['label' => 'QV Delivery Notification', 'value' => '8'],
        ];
    }
}
