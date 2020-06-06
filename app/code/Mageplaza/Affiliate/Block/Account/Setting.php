<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Account;

/**
 * Dashboard Customer Info
 */
class Setting extends \Mageplaza\Affiliate\Block\Account
{
	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__('Affiliate Setting'));

		return parent::_prepareLayout();
	}

	public function getEmailNotification()
	{
		return $this->getCurrentAccount()->getEmailNotification();
	}
}
