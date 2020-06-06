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


class Displayrefer implements \Magento\Framework\Option\ArrayInterface
{
	const CATEGORY_PAGE = 'list';
	const PRODUCT_PAGE = 'detail';

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
		return [self::CATEGORY_PAGE => __('Category page'), self::PRODUCT_PAGE => __('Product page')];
	}
}
