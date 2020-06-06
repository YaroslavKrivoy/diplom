<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Account;


class Refer extends \Mageplaza\Affiliate\Block\Account
{
	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__('Refer friends'));

		return parent::_prepareLayout();
	}

	public function getSendmailUrl()
	{
		return $this->getUrl('*/*/referemail');
	}

	public function getSharingUrl()
	{
		return $this->_affiliateHelper->getSharingUrl();
	}

	public function getSharingEmail()
	{
		return $this->getCustomer()->getEmail();
	}

	public function getSharingCode()
	{
		return $this->getCurrentAccount()->getCode();
	}

	public function getSharingTitle()
	{

		return $this->_affiliateHelper->getAffiliateConfig('refer/sharing_content/subject');
	}

	public function getSocialContent()
	{
		$content    = $this->_affiliateHelper->getAffiliateConfig('refer/sharing_content/social_content');
		$storeModel = $this->_storeManager->getStore();

		return str_replace(array(
			'{{store_name}}',
			'{{url}}'
		), array(
			$storeModel->getFrontendName(),
			$this->getSharingUrl()
		), $content);
	}

	public function getEmailContent()
	{
		$content    = $this->_affiliateHelper->getAffiliateConfig('refer/sharing_content/email_content');
		$storeModel = $this->_storeManager->getStore();
		return str_replace(array(
			'{{store_name}}',
			'{{url}}',
			'{{account_name}}',
			'{{code}}'
		), array(
			$storeModel->getFrontendName(),
			$this->getSharingUrl(),
			$this->getCustomer()->getName(),
			$this->getSharingCode()
		), $content);
	}
}
