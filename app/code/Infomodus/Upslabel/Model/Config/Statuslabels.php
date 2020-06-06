<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class Statuslabels implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Success')],
            ['value' => '1', 'label' => __('Error')],
        ];
    }
    public function getStatus()
    {
        return [
            'success' => __('Success'),
            'error' => __('Error'),
            'notcreated' => __('Not created'),
            'pending' => __('UPS Pending'),
        ];
    }
}
