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

class Month implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $c = [];
        for ($i=1; $i<13; $i++) {
            $c[] = ['label' => $i, 'value' => ($i<10?'0'.$i:$i)];
        }

        return $c;
    }
}