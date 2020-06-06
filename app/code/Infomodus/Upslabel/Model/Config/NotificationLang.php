<?php
namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class NotificationLang implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $c = [
            ['label' => __('Default for country'), 'value' => ""],
            ['label' => __('DAN'), 'value' => "DAN:97"],
            ['label' => __('DEU'), 'value' => "DEU:97"],
            ['label' => __('ENG (dialect GB)'), 'value' => "ENG:GB"],
            ['label' => __('ENG (dialect US)'), 'value' => "ENG:US"],
            ['label' => __('ENG (dialect CA)'), 'value' => "ENG:CA"],
            ['label' => __('FIN'), 'value' => "FIN:97"],
            ['label' => __('FRA'), 'value' => "FRA:97"],
            ['label' => __('FRA (dialect CA)'), 'value' => "FRA:CA"],
            ['label' => __('ITA'), 'value' => "ITA:97"],
            ['label' => __('NLD'), 'value' => "NLD:97"],
            ['label' => __('POR'), 'value' => "POR:97"],
            ['label' => __('SPA'), 'value' => "SPA:97"],
            ['label' => __('SPA (dialect PR)'), 'value' => "SPA:PR"],
            ['label' => __('SWE'), 'value' => "SWE:97"],
            ['label' => __('NOR'), 'value' => "NOR:97"],
            ['label' => __('POL'), 'value' => "POL:97"],
            ['label' => __('CES'), 'value' => "CES:97"],
            ['label' => __('ELL'), 'value' => "ELL:97"],
            ['label' => __('HEB'), 'value' => "HEB:97"],
            ['label' => __('HUN'), 'value' => "HUN:97"],
            ['label' => __('RUS'), 'value' => "RUS:97"],
            ['label' => __('SLK'), 'value' => "SLK:97"],
            ['label' => __('TUR'), 'value' => "TUR:97"],
            ['label' => __('VIE'), 'value' => "VIE:97"],
            ['label' => __('ZHO'), 'value' => "ZHO:97"],
            ['label' => __('RON'), 'value' => "RON:97"],
        ];
        return $c;
    }
}
