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


class Showlink implements \Magento\Framework\Option\ArrayInterface
{
	const SHOW_ON_TOP_LINK = 'top_link';
	const SHOW_ON_FOOTER_LINK = 'footer_link';

	public function toOptionArray()
	{
		$optionArray = array();
		$optionArray[] = array('value' => '', 'label' => __('-- Please Select --'));

		foreach($this->toArray() as $key => $value){
			$optionArray[] = array('value' => $key, 'label' => $value);
		}

		return $optionArray;
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [self::SHOW_ON_FOOTER_LINK => __('Footer Link'), self::SHOW_ON_TOP_LINK => __('Top Link')];
	}
}
