<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class PaperSize implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'A5', 'value' => 'A5'],
            ['label' => 'A4', 'value' => 'A4'],
            ['label' => __('Custom'), 'value' => 'AC'],
        ];
    }
}
