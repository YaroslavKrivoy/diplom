<?php

namespace Mageplaza\Affiliate\Helper;

class Calculation extends Data
{
	protected $_address;

	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	)
	{
		$this->_address = $shippingAssignment->getShipping()->getAddress();

		return $this;
	}

	public function canCalculate($firstOrder = false)
	{
		$cacheKey = 'affiliate_can_calculate';
		if (!$this->hasCache($cacheKey)) {
			$this->saveCache($cacheKey, false);
			$account = $this->getCurrentAffiliate();
			if (!$account->getId() && ($key = $this->getAffiliateKey())) {
				$account = $this->accountFactory->create()->loadByCode($key);
				if ($account->getId() && $account->isActive()) {
					$this->saveCache('affiliate_account_refer_' . $key, $account);
					$campaigns = $this->getAvailableCampaign($account);
					if (sizeof($campaigns)) {
						$this->saveCache($cacheKey, true);
					}
				}
			}
		}

		$firstOrderCacheKey = 'affiliate_can_calculate_first_order';
		if (!$this->hasCache($firstOrderCacheKey)) {
			$this->saveCache($firstOrderCacheKey, true);
			if ($firstOrder) {
				$this->saveCache($firstOrderCacheKey, !$this->hasFirstOrder());
			}
		}

		return $this->getCache($cacheKey) && $this->getCache($firstOrderCacheKey);
	}

	public function getAvailableCampaign($account = null)
	{
		if(is_null($account)){
			$account = $this->getCurrentAffiliate();
		}

		$cacheKey = 'affiliate_available_campaign_' . $account->getId();
		if (!$this->hasCache($cacheKey)) {
			$campaigns      = $this->campaignFactory->create()->getCollection()
				->getAvailableCampaign($account->getGroupId(), $this->storeManager->getWebsite()->getId());
			$campaignResult = array();
			foreach ($campaigns as $campaign) {
				$campaign->afterLoad();
				if ($campaign->validate($this->_address)) {
					$campaignResult[] = $campaign;
				}
			}

			$this->saveCache($cacheKey, $campaignResult);
		}

		return $this->getCache($cacheKey);
	}
}