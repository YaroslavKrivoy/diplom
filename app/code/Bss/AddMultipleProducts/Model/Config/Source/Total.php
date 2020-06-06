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

class Total implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
                ['value' => 0, 'label' => __('No')],
                ['value' => 1, 'label' => __('Total products')],
                ['value' => 2, 'label' => __('Total qty')]
                ];
    }

    public function toArray()
    {
        return [0 => __('No'), 1 => __('Total products'), 2 => __('Total qty')];
    }
}
