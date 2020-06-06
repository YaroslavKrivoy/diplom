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

class Weight implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('LBS'), 'value' => 'LBS'],
            ['label' => __('KGS'), 'value' => 'KGS'],
        ];
    }

    public function getArray()
    {
        return [
            'LBS' => __('LBS'),
            'KGS' => __('KGS'),
        ];
    }
}
