<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AddMultipleProducts
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AddMultipleProducts\Model\Config\Source;

class Button implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
                    ['value' => 0, 'label' => __('Top')],
                    ['value' => 1, 'label' => __('Bottom')],
                    ['value' => 2, 'label' => __('Both (Top and Bottom)')],
                    ['value' => 3, 'label' => __('Right (Scroll)')]
                ];
    }

    public function toArray()
    {
        return [0 => __('Top'), 1 => __('Bottom'), 2 => __('Both (Top and Bottom)'), 3 => __('Right (Scroll)')];
    }
}
