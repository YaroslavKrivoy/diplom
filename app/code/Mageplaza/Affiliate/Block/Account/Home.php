<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Account;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Dashboard Customer Info
 */
class Home extends \Mageplaza\Affiliate\Block\Account
{
	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__('My Credit'));

		return parent::_prepareLayout();
	}

	/**
	 * get show credit status
	 *
	 * @return mixed
	 */
	public function getTotalShow()
	{
		return array(
			'balance'          => array(
				'label' => __('Available Balance'),
				'value' => $this->getCurrentAccount()->getBalance()
			),
			'holding_balance'  => array(
				'label' => __('Holding Balance'),
				'value' => $this->getCurrentAccount()->getHoldingBalance()
			),
			'total_commission' => array(
				'label' => __('Total Earned'),
				'value' => $this->getCurrentAccount()->getTotalCommission()
			),
			'total_paid'       => array(
				'label' => __('Total Paid'),
				'value' => $this->getCurrentAccount()->getTotalPaid()
			),
		);
	}
}
