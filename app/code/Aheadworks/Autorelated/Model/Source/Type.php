<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 *
 * @package Aheadworks\Autorelated\Model\Source
 */
class Type implements ArrayInterface
{
    /**#@+
     * Listing type
     */
    const PRODUCT_BLOCK_TYPE = 1;
    const CART_BLOCK_TYPE = 2;
    const CATEGORY_BLOCK_TYPE = 3;
    const CUSTOM_BLOCK_TYPE = 4;
    /**#@-*/

    /**
     * Return listing type
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRODUCT_BLOCK_TYPE,
                'label' => __('Related Product'),
            ],
            [
                'value' => self::CART_BLOCK_TYPE,
                'label' => __('Shopping Cart'),
            ],
            [
                'value' => self::CATEGORY_BLOCK_TYPE,
                'label' => __('Category'),
            ],
            [
                'value' => self::CUSTOM_BLOCK_TYPE,
                'label' => __('Custom position'),
            ],
        ];
    }

    /**
     * Retrieve tooltip text for product condition type column header of rule grid form
     *
     * @return \Magento\Framework\Phrase
     */
    public function getProductConditionTypeTooltip($type = null)
    {
        $tooltipText = __("This column shows the mode the related products are displayed in:<br>"
            . "- <b>Conditions</b> - based on specified conditions;<br>"
            . "- <b>Who Bought This Also Bought</b> (WBTAB) - based on purchase history;<br>"
            . "- <b>Who Viewed This Also Viewed</b> (WVTAV) - based on views history;<br>"
            . "- <b>Conditions Combination</b> mode only is available for Category and Custom Position rule types;");
        return $tooltipText;
    }
}
