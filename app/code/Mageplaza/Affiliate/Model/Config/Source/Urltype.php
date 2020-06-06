<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Mageplaza\Affiliate\Model\Config\Source;


class Urltype implements \Magento\Framework\Option\ArrayInterface
{
	const TYPE_HASH = 'hash';
	const TYPE_PARAM = 'param';

	public function toOptionArray()
	{
		$array = array();
		foreach ($this->getOptionHash() as $key => $label) {
			$array[] = array(
				'value' => $key,
				'label' => $label
			);
		}

		return $array;
	}


	public function getOptionHash()
	{
		$array = array(
			self::TYPE_HASH  => __('Hash'),
			self::TYPE_PARAM => __('Parameter')
		);

		return $array;
	}
}
