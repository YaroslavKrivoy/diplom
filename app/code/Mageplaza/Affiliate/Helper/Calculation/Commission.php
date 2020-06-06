<?php

namespace Mageplaza\Affiliate\Helper\Calculation;

use Mageplaza\Affiliate\Helper\Calculation;

class Commission extends Calculation
{
	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	)
	{
		parent::collect($quote, $shippingAssignment, $total);
		if (!$this->canCalculate()) {
			return $this;
		}

		$baseGrandTotal   = array_sum($total->getAllBaseTotalAmounts());
		$commissionResult = [];
		$fieldSubfix      = $this->hasFirstOrder() ? '_second' : '';

		$account = $this->getCache('affiliate_account_refer_' . $this->getAffiliateKeyFromCookie());
		$tree    = $this->getAffiliateTree($account);
		foreach ($this->getAvailableCampaign($account) as $campaign) {
			$commissions = @unserialize($campaign->getCommission());
			if($commissions && (is_array($commissions)||is_object($commissions))){
				foreach ($commissions as $key => $tier) {
					if (!isset($tree[$key])) {
						break;
					}
					$tierId = $tree[$key];
					if (!isset($commissionResult[$tierId])) {
						$commissionResult[$tierId] = 0;
					}
					$commission = $tier['value' . $fieldSubfix];
					switch ($tier['type' . $fieldSubfix]) {
						case \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission::TYPE_FIXED:
							$commissionResult[$tierId] += $this->priceCurrency->round($commission);
							break;
						case \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission::TYPE_SALE_PERCENT:
							$commissionResult[$tierId] += $this->priceCurrency->round($baseGrandTotal * $commission / 100);
							break;
						default:
							break;
					}
				}
			}
		}

		$total->setAffiliateCommission(serialize($commissionResult));

		return $this;
	}

	public function adminCollect($account, $amount){
		$commissionResult = [];
		$fieldSubfix      = $this->hasFirstOrder() ? '_second' : '';

		$tree    = $this->getAffiliateTree($account);
		foreach ($this->getAvailableCampaign($account) as $campaign) {
			$commissions = @unserialize($campaign->getCommission());
			if($commissions && (is_array($commissions)||is_object($commissions))){
				foreach ($commissions as $key => $tier) {
					if (!isset($tree[$key])) {
						break;
					}
					$tierId = $tree[$key];
					if (!isset($commissionResult[$tierId])) {
						$commissionResult[$tierId] = 0;
					}
					$commission = $tier['value' . $fieldSubfix];
					switch ($tier['type' . $fieldSubfix]) {
						case \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission::TYPE_FIXED:
							$commissionResult[$tierId] += $this->priceCurrency->round($commission);
							break;
						case \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission::TYPE_SALE_PERCENT:
							$commissionResult[$tierId] += $this->priceCurrency->round($amount * $commission / 100);
							break;
						default:
							break;
					}
				}
			}
		}

		return $commissionResult;
	}

	public function getAffiliateTree($account, $numOfTier = null)
	{
		$tree = explode('/', $account->getTree());

		if ($numOfTier) {
			while (sizeof($tree) > $numOfTier) {
				array_shift($tree);
			}
		}

		$treeResult = [];
		$tier       = 1;
		while (sizeof($tree)) {
			$treeResult['tier_' . $tier++] = array_pop($tree);
		}

		return $treeResult;
	}
}