<?php

namespace Mageplaza\Affiliate\Helper\Calculation;

use Mageplaza\Affiliate\Helper\Calculation;

class Discount extends Calculation
{
	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	)
	{
		parent::collect($quote, $shippingAssignment, $total);

		if (!$this->canCalculate(true)) {
			return $this;
		}

		$discountAmount     = 0;
		$baseDiscountAmount = 0;

		$baseGrandTotal            = array_sum($total->getAllBaseTotalAmounts());
		$baseGrandTotalWithoutShip = $baseGrandTotal - $total->getBaseShippingAmount();

		$account = $this->getCache('affiliate_account_refer_' . $this->getAffiliateKeyFromCookie());
		foreach ($this->getAvailableCampaign($account) as $campaign) {
			$maxDiscount   = $campaign->getApplyToShipping() ? $baseGrandTotal : $baseGrandTotalWithoutShip;
			$discountValue = $campaign->getDiscountAmount();
			switch ($campaign->getDiscountAction()) {
				case \Mageplaza\Affiliate\Model\Campaign\Discount::CART_FIXED:
					$baseDiscountAmount += min($discountValue, $maxDiscount);
					break;
				case \Mageplaza\Affiliate\Model\Campaign\Discount::PERCENT:
					$baseDiscountAmount += $maxDiscount * $discountValue / 100;
					break;
				default:
					break;
			}
			$discountAmount += $this->priceCurrency->convert($baseDiscountAmount);
		}

		if($discountAmount) {
			$total->addTotalAmount('affiliate_discount', -$discountAmount);
			$total->addBaseTotalAmount('affiliate_discount', -$baseDiscountAmount);
		}

		return $this;
	}
}