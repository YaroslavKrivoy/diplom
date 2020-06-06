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
namespace Mageplaza\Affiliate\Model\Withdraw;

class Status implements \Magento\Framework\Option\ArrayInterface
{
	const PENDING = 1;
	const COMPLETE = 2;
	const CANCEL = 3;


	/**
	 * to option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = [];

		foreach ($this->getOptionHash() as $value => $label) {
			$options[] = [
				'value' => $value,
				'label' => $label
			];
		}

		return $options;
	}

	public function getOptionHash()
	{
		return [
			self::PENDING  => __('Pending'),
			self::COMPLETE => __('Complete'),
			self::CANCEL   => __('Cancel')
		];
	}
}
