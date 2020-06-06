<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Source;

/**
 * Class Template
 *
 * @package Aheadworks\Autorelated\Model\Source
 */
class Template implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Template variable
     */
    const GRID = 1;
    const SLIDER = 2;
    /**#@-*/

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::GRID, 'label' => __('Grid')],
            ['value' => self::SLIDER, 'label' => __('Slider')],
        ];
    }
}
