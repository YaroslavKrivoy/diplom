<?php
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class DutyAndTaxInternational implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $c = [
            ['label' => __('shipper pays transportation fees and receiver pays duties and taxes'),
                'value' => 'customer'],
            ['label' => __('shipper pays both transportation fees and duties and taxes'),
                'value' => 'shipper'],
        ];
        return $c;
    }
}
