<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Model\Campaign;

class Discount implements \Magento\Framework\Option\ArrayInterface
{
	const PERCENT = 'by_percent';
	const FIXED = 'by_fixed';
	const CART_FIXED = 'cart_fixed';
	const BUY_X_GET_Y = 'buy_x_get_y';


	/**
	 * to option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = [
			[
				'value' => self::PERCENT,
				'label' => __('Percent of cart total')
//				'label' => __('Percent of product price discount')
			],
//			[
//				'value' => self::FIXED,
//				'label' => __('Fixed amount discount')
//			],
			[
				'value' => self::CART_FIXED,
				'label' => __('Fixed amount discount for whole cart')
			],
//			[
//				'value' => self::BUY_X_GET_Y,
//				'label' => __('Buy X get Y free (discount amount is Y)')
//			],
		];

		return $options;

	}
}
