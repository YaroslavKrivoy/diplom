<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class ReasonForExport implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'SALE', 'value' => 'SALE'],
            ['label' => 'GIFT', 'value' => 'GIFT'],
            ['label' => 'SAMPLE', 'value' => 'SAMPLE'],
            ['label' => 'RETURN', 'value' => 'RETURN'],
            ['label' => 'REPAIR', 'value' => 'REPAIR'],
            ['label' => 'INTERCOMPANYDATA', 'value' => 'INTERCOMPANYDATA'],
        ];
    }
}
