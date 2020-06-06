<?php
/**
 * Created by PhpStorm.
 * User: Vitalij
 * Date: 01.10.14
 * Time: 11:55
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class TermsOfShipment implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => 'Cost and Freight', 'value' => 'CFR'],
            ['label' => 'Cost, Insurance and Freight', 'value' => 'CIF'],
            ['label' => 'Carriage and Insurance Paid', 'value' => 'CIP'],
            ['label' => 'Carriage Paid To', 'value' => 'CPT'],
            ['label' => 'Delivered at Frontier', 'value' => 'DAF'],
            ['label' => 'Delivery Duty Paid', 'value' => 'DDP'],
            ['label' => 'Delivery Duty Unpaid', 'value' => 'DDU'],
            ['label' => 'Delivered Ex Quay', 'value' => 'DEQ'],
            ['label' => 'Delivered Ex Ship', 'value' => 'DES'],
            ['label' => 'Ex Works', 'value' => 'EXW'],
            ['label' => 'Free Alongside Ship', 'value' => 'FAS'],
            ['label' => 'Free Carrier', 'value' => 'FCA'],
            ['label' => 'Free On Board', 'value' => 'FOB'],
        ];
    }
}
