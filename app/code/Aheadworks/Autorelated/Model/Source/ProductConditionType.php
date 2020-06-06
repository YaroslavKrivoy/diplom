<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Source;

/**
 * Class ProductConditionType
 *
 * @package Aheadworks\Autorelated\Model\Source
 */
class ProductConditionType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Product condition types
     */
    const CONDITIONS_COMBINATION = 1;
    const WHO_BOUGHT_THIS_ALSO_BOUGHT = 2;
    const WHO_VIEWED_THIS_ALSO_VIEWED = 3;
    const DEFAULT_TYPE = self::CONDITIONS_COMBINATION;
    /**#@-*/

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CONDITIONS_COMBINATION, 'label' => __('Conditions Combination')],
            ['value' => self::WHO_BOUGHT_THIS_ALSO_BOUGHT, 'label' => __('Who Bought This Also Bought')],
            ['value' => self::WHO_VIEWED_THIS_ALSO_VIEWED, 'label' => __('Who Viewed This Also Viewed')],
        ];
    }
}
