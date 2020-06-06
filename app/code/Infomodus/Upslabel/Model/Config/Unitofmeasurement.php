<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 07.02.12
 * Time: 10:49
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Unitofmeasurement implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            'IN' => __('Inches'),
            'CM' => __('Centimeters'),
            '00' => __('Metric Units of Measurement'),
            '01' => __('English Units of Measurement'),
        ];
    }

    public function getArray()
    {
        return [
            'IN' => __('Inches'),
            'CM' => __('Centimeters'),
            '00' => __('Metric Units of Measurement'),
            '01' => __('English Units of Measurement'),
        ];
    }
}
