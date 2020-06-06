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
namespace Mageplaza\Affiliate\Model\Banner;

class Status implements \Magento\Framework\Option\ArrayInterface
{
	const ENABLED = 1;
	const DISABLED = 2;


	/**
	 * to option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = [
			[
				'value' => self::ENABLED,
				'label' => __('Enabled')
			],
			[
				'value' => self::DISABLED,
				'label' => __('Disabled')
			],
		];

		return $options;
	}
}
