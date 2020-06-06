<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config\Pickup;

use Magento\Framework\Data\OptionSourceInterface;

class Year implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => date("Y"), 'value' => date("Y")],
            ['label' => date("Y")+1, 'value' => date("Y")+1],
        ];
    }
}
