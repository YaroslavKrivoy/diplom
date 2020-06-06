<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Account;


class Banner extends \Mageplaza\Affiliate\Block\Account
{
	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__('Affiliate Banners'));

		return parent::_prepareLayout();
	}

	public function getAvailableBanners()
	{
		$campaigns = $this->campaignFactory->create()->getCollection()
			->getAvailableCampaign($this->getCurrentAccount()->getGroupId(), $this->_storeManager->getWebsite()->getId())
			->getColumnValues('campaign_id');

		$banner           = $this->objectManager->create('Mageplaza\Affiliate\Model\Banner');
		$bannerCollection = $banner->getCollection()
			->addFieldToFilter('campaign_id', ['in' => $campaigns])
			->addFieldToFilter('status', \Mageplaza\Affiliate\Model\Banner\Status::ENABLED);

		return $bannerCollection;
	}

	public function getLink($banner)
	{
		$url       = $banner->getLink();
		$validator = new \Zend\Validator\Uri();
		if (!$validator->isValid($url)) {
			$url = $this->_storeManager->getStore()->getBaseUrl();
		}

		return $this->_affiliateHelper->getSharingUrl($url, ['source' => 'banner', 'key' => $banner->getId()], \Mageplaza\Affiliate\Model\Config\Source\Urltype::TYPE_PARAM);
	}

	public function getContentText($banner)
	{
		return $this->getBannerLink($banner, $banner->getContentHtml());
	}

	public function getBannerLink($banner, $text)
	{
		$html = '<a href="' . $this->getLink($banner) . '" ' . ($banner->getRelNofollow() ? "rel='nofollow' " : '') . 'target="_blank">';
		$html .= $text;
		$html .= '</a>';

		return $html;
	}
}
