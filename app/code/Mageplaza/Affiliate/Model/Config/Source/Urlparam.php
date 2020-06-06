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


class Urlparam implements \Magento\Framework\Option\ArrayInterface
{
	const PARAM_ID = 'account_id';
	const PARAM_CODE = 'code';

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
			self::PARAM_ID  => __('Affiliate ID'),
			self::PARAM_CODE => __('Affiliate Code')
		);

		return $array;
	}
}
