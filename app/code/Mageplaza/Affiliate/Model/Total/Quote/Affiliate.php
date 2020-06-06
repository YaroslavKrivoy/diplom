<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Model\Total\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Item;

/**
 * Class Affiliate
 * @package Mageplaza\Affiliate\Model\Total\Quote
 */
class Affiliate extends AbstractTotal
{
	protected $_discountHelper;
	protected $_commissionHelper;

	/**
	 * Constructor
	 *
	 * @param \Mageplaza\Affiliate\Helper\Calculation\Discount $discountHelper
	 * @param \Mageplaza\Affiliate\Helper\Calculation\Commission $commissionHelper
	 */
	public function __construct(
		\Mageplaza\Affiliate\Helper\Calculation\Discount $discountHelper,
		\Mageplaza\Affiliate\Helper\Calculation\Commission $commissionHelper
	)
	{
		$this->_discountHelper   = $discountHelper;
		$this->_commissionHelper = $commissionHelper;
	}

	/**
	 * Collect address subtotal
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
	 * @param Address\Total $total
	 * @return $this
	 */
	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	)
	{
		parent::collect($quote, $shippingAssignment, $total);

		if (($quote->isVirtual() && ($this->_address->getAddressType() == 'shipping')) ||
			(!$quote->isVirtual() && ($this->_address->getAddressType() == 'billing'))
		) {
			return $this;
		}

		$this->_commissionHelper->collect($quote, $shippingAssignment, $total);
		$this->_discountHelper->collect($quote, $shippingAssignment, $total);

		$quote->addData([
			'affiliate_key'                  => $this->_discountHelper->getAffiliateKeyFromCookie(),
			'affiliate_commission'           => $total->getAffiliateCommission(),
			'affiliate_discount_amount'      => $total->getTotalAmount('affiliate_discount'),
			'base_affiliate_discount_amount' => $total->getBaseTotalAmount('affiliate_discount'),
		]);

		return $this;
	}

	/**
	 * Assign subtotal amount and label to address object
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param Address\Total $total
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
	{
		$result = null;
		$amount = $total->getAffiliateDiscountAmount();

		if ($amount != 0) {
			$result = [
				'code'  => $this->getCode(),
				'title' => __('Affiliate Discount'),
				'value' => $amount
			];
		}

		return $result;
	}
}
