<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class ListsType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'shipment', 'label' => __('Shipment')],
            ['value' => 'refund', 'label' => __('Refund')],
            ['value' => 'invert', 'label' => __('Invert shipment')],
        ];
    }

    public function getTypes()
    {
        return [
            'shipment' => __('Shipment'),
            'refund' => __('Refund'),
            'invert' => __('Invert shipment'),
        ];
    }
}
