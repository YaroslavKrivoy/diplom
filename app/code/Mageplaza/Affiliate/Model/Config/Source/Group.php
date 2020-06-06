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

use Mageplaza\Affiliate\Model\GroupFactory;

class Group implements \Magento\Framework\Option\ArrayInterface
{

	protected $_groupFactory;
	protected $_options;

	public function __construct(
		GroupFactory $groupFactory
	)
	{
		$this->_groupFactory = $groupFactory;
	}

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$this->_options      = array();
		$groupModel             = $this->_groupFactory->create();
		$groupCollection   = $groupModel->getCollection();
		foreach ($groupCollection as $item) {
			$data['value'] = $item->getId();
			$data['label'] = $item->getName();
			$this->_options[] = $data;
		}
		return $this->_options;
	}
}
