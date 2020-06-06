<?php
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class DimensionsType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $c = [
            ['value' => 0, 'label' => 'Automatic calculation'],
            ['value' => 1, 'label' => 'Static box'],
        ];

        return $c;
    }
}
