<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class BulkPrinting implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('All labels'), 'value' => 0],
            ['label' => __('Unprinted labels only'), 'value' => 1],
        ];
    }
}
