<?php
namespace Infomodus\Upslabel\Model\Config\Pickup;

use Magento\Framework\Data\OptionSourceInterface;

class Overweightindicator implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Not over weight'), 'value' => 'N'],
            ['label' => __('Over weight'), 'value' => 'Y'],
        ];
    }
}
