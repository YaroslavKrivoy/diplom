<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block;

/**
 * Links list block
 */
class Navigation extends \Magento\Framework\View\Element\Html\Links
{
	protected $_helper;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Mageplaza\Affiliate\Helper\Data $helper,
		array $data = [])
	{
		$this->_helper = $helper;
		parent::__construct($context, $data);
	}

	/**
	 * Get links
	 *
	 * @return \Magento\Framework\View\Element\Html\Link[]
	 */
	public function getLinks()
	{
		$links = $this->_layout->getChildBlocks($this->getNameInLayout());

		$isGuest = true;
		$account = $this->_helper->getCurrentAffiliate();
		if ($account && $account->getId() && $account->isActive()) {
			$isGuest = false;
		}

		foreach ($links as $key => $block) {
			if (($isGuest && ($block->getActive() == 'login')) || (!$isGuest && ($block->getActive() == 'guess'))) {
				unset($links[$key]);
			}
		}

		usort($links, function ($a, $b) {
			return $a->getSortOrder() - $b->getSortOrder();
		});

		return $links;
	}
}
