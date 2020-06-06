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

class PrintType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Image and PDF'), 'value' => 'GIF'],
            ['label' => 'EPL2', 'value' => 'EPL'],
            ['label' => 'SPL', 'value' => 'SPL'],
            ['label' => 'ZPL', 'value' => 'ZPL'],
            ['label' => 'STAR', 'value' => 'STARPL'],
        ];
    }
}
