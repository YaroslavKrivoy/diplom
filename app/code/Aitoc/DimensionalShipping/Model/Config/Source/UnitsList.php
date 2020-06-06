<?php

namespace Aitoc\DimensionalShipping\Model\Config\Source;

class UnitsList implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'mm', 'label' => __('mm')],
            ['value' => 'cm', 'label' => __('cm')],
            ['value' => 'in', 'label' => __('in')],
            ['value' => 'ft', 'label' => __('ft')],
        ];
    }
}
