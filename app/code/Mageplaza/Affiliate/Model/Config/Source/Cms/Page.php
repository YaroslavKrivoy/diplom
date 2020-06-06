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

use Magento\Cms\Model\PageFactory;

class Page implements \Magento\Framework\Option\ArrayInterface
{

	protected $_cms;
	protected $_options;

	public function __construct(
		PageFactory $pageFactory
	)
	{
		$this->_cms = $pageFactory;
	}

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$this->_options      = array();
		$existingIdentifiers = array();
		$cmsPage             = $this->_cms->create();
		$cmsPageCollection   = $cmsPage->getCollection();
		foreach ($cmsPageCollection as $item) {
			$identifier = $item->getData('identifier');

			$data['value'] = $identifier;
			$data['label'] = $item->getData('title');

			if (in_array($identifier, $existingIdentifiers)) {
				$data['value'] .= '|' . $item->getData('page_id');
			} else {
				$existingIdentifiers[] = $identifier;
			}

			$this->_options[] = $data;
		}


		return $this->_options;
	}
}
