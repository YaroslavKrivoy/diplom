<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Mageplaza\Affiliate\Model\Config\Source\Cms;

use Magento\Cms\Model\BlockFactory;

class Block implements \Magento\Framework\Option\ArrayInterface
{

	protected $_cms;
	protected $_options;

	public function __construct(
		BlockFactory $blockFactory
	)
	{
		$this->_cms = $blockFactory;
	}

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$cmsBlock           = $this->_cms->create();
		$cmsBlockCollection = $cmsBlock->getCollection();
		if (!$this->_options) {
			foreach ($cmsBlockCollection as $item) {
				$this->_options[] = array(
					'label' => $item->getData('title'),
					'value' => $item->getData('identifier')
				);
			}
		}


		return $this->_options;
	}
}
