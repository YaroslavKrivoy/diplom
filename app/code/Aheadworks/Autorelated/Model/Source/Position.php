<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Source;

/**
 * Class Position
 *
 * @package Aheadworks\Autorelated\Model\Source
 */
class Position implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Product position
     */
    const PRODUCT_INSTEAD_NATIVE_RELATED_BLOCK = 1;
    const PRODUCT_BEFORE_NATIVE_RELATED_BLOCK = 2;
    const PRODUCT_AFTER_NATIVE_RELATED_BLOCK = 3;
    const PRODUCT_INSTEAD_NATIVE_UP_SELLS_BLOCK = 18;
    const PRODUCT_BEFORE_NATIVE_UP_SELLS_BLOCK = 19;
    const PRODUCT_AFTER_NATIVE_UP_SELLS_BLOCK = 20;
    const PRODUCT_CONTENT_TOP = 4;
    const PRODUCT_CONTENT_BOTTOM = 5;
    const PRODUCT_SIDEBAR_TOP = 6;
    const PRODUCT_SIDEBAR_BOTTOM = 7;
    /**#@-*/

    /**#@+
     * Cart position
     */
    const CART_INSTEAD_NATIVE_CROSSSELLS_BLOCK = 10;
    const CART_BEFORE_NATIVE_CROSSSELLS_BLOCK = 11;
    const CART_AFTER_NATIVE_CROSSSELLS_BLOCK = 12;
    const CART_CONTENT_TOP = 13;
    const CART_CONTENT_BOTTOM = 14;
    /**#@-*/

    /**#@+
     * Category position
     */
    const CATEGORY_CONTENT_TOP = 15;
    const CATEGORY_CONTENT_BOTTOM = 16;
    /**#@-*/

    /**#@+
     * Custom position
     */
    const CUSTOM = 17;
    /**#@-*/

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $newArray = [];
        $positions = $this->toOptionArray();

        foreach ($positions as $position) {
            $newArray[$position['value']] = $position['label'];
        }
        return $newArray;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRODUCT_INSTEAD_NATIVE_RELATED_BLOCK,
                'label' => __('Product page. Instead of native related block')
            ],
            [
                'value' => self::PRODUCT_BEFORE_NATIVE_RELATED_BLOCK,
                'label' => __('Product page. Before native related block')
            ],
            [
                'value' => self::PRODUCT_AFTER_NATIVE_RELATED_BLOCK,
                'label' => __('Product page. After native related block')
            ],
            [
                'value' => self::PRODUCT_INSTEAD_NATIVE_UP_SELLS_BLOCK,
                'label' => __('Product page. Instead of native Up-sells block')
            ],
            [
                'value' => self::PRODUCT_BEFORE_NATIVE_UP_SELLS_BLOCK,
                'label' => __('Product page. Before native Up-sells block')
            ],
            [
                'value' => self::PRODUCT_AFTER_NATIVE_UP_SELLS_BLOCK,
                'label' => __('Product page. After native Up-sells block')
            ],
            [
                'value' => self::PRODUCT_CONTENT_TOP,
                'label' => __('Product page. Content top')
            ],
            [
                'value' => self::PRODUCT_CONTENT_BOTTOM,
                'label' => __('Product page. Content bottom')
            ],
            [
                'value' => self::PRODUCT_SIDEBAR_TOP,
                'label' => __('Product page. Sidebar top')
            ],
            [
                'value' => self::PRODUCT_SIDEBAR_BOTTOM,
                'label' => __('Product page. Sidebar bottom')
            ],
            [
                'value' => self::CART_INSTEAD_NATIVE_CROSSSELLS_BLOCK,
                'label' => __('Shopping cart page. Instead of native cross-sells block')
            ],
            [
                'value' => self::CART_BEFORE_NATIVE_CROSSSELLS_BLOCK,
                'label' => __('Shopping cart page. Before native cross-sells block')
            ],
            [
                'value' => self::CART_AFTER_NATIVE_CROSSSELLS_BLOCK,
                'label' => __('Shopping cart page. After native cross-sells block')
            ],
            [
                'value' => self::CART_CONTENT_TOP,
                'label' => __('Shopping cart page. Content top')
            ],
            [
                'value' => self::CART_CONTENT_BOTTOM,
                'label' => __('Shopping cart page. Content bottom')
            ],
            [
                'value' => self::CATEGORY_CONTENT_TOP,
                'label' => __('Category page. Content top')
            ],
            [
                'value' => self::CATEGORY_CONTENT_BOTTOM,
                'label' => __('Category page. Content bottom')
            ],
            [
                'value' => self::CUSTOM,
                'label' => __('Custom position')
            ],
        ];
    }

    /**
     * @return array
     */
    public function getProductPositions()
    {
        return [
            self::PRODUCT_INSTEAD_NATIVE_RELATED_BLOCK,
            self::PRODUCT_BEFORE_NATIVE_RELATED_BLOCK,
            self::PRODUCT_AFTER_NATIVE_RELATED_BLOCK,
            self::PRODUCT_INSTEAD_NATIVE_UP_SELLS_BLOCK,
            self::PRODUCT_BEFORE_NATIVE_UP_SELLS_BLOCK,
            self::PRODUCT_AFTER_NATIVE_UP_SELLS_BLOCK,
            self::PRODUCT_CONTENT_TOP,
            self::PRODUCT_CONTENT_BOTTOM,
            self::PRODUCT_SIDEBAR_TOP,
            self::PRODUCT_SIDEBAR_BOTTOM,
        ];
    }

    /**
     * @return array
     */
    public function getCartPositions()
    {
        return [
            self::CART_INSTEAD_NATIVE_CROSSSELLS_BLOCK,
            self::CART_BEFORE_NATIVE_CROSSSELLS_BLOCK,
            self::CART_AFTER_NATIVE_CROSSSELLS_BLOCK,
            self::CART_CONTENT_TOP,
            self::CART_CONTENT_BOTTOM,
        ];
    }

    /**
     * @return array
     */
    public function getCategoryPositions()
    {
        return [
            self::CATEGORY_CONTENT_TOP,
            self::CATEGORY_CONTENT_BOTTOM
        ];
    }

    /**
     * @return array
     */
    public function getCustomPositions()
    {
        return [
            self::CUSTOM
        ];
    }

    /**
     * @param int $position
     * @return array
     */
    public function getPositionLabel($position)
    {
        $allPositions = $this->getOptionArray();
        if (!array_key_exists($position, $allPositions)) {
            return '';
        }
        return $allPositions[$position];
    }
}
