<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Source;

/**
 * Class Sort
 *
 * @package Aheadworks\Autorelated\Model\Source
 */
class Sort implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Sort variable
     */
    const SORT_BY_BESTSELLER = 1;
    const SORT_BY_NEWEST = 2;
    const SORT_BY_PRICE_DESC = 3;
    const SORT_BY_PRICE_ASC = 4;
    const SORT_BY_RANDOM = 5;
    /**#@-*/

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SORT_BY_BESTSELLER, 'label' => __('Bestsellers')],
            ['value' => self::SORT_BY_NEWEST, 'label' => __('Newest')],
            ['value' => self::SORT_BY_PRICE_DESC, 'label' => __('Price: high to low')],
            ['value' => self::SORT_BY_PRICE_ASC, 'label' => __('Price: low to high')],
            ['value' => self::SORT_BY_RANDOM, 'label' => __('Random')],
        ];
    }
}
