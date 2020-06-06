<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Account\Home;

/**
 * Dashboard Customer Info
 */
class Transaction extends \Mageplaza\Affiliate\Block\Account\Home
{
	public function getTransactions()
	{
		$collection = $this->transactionFactory->create()
			->getCollection()
			->addFieldToFilter('account_id', $this->getCurrentAccount()->getId());

		if ($collection->getSize()) {
			// create pager block for collection
			$pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'affiliate.transaction.pager');
			// assign collection to pager
			$pager->setLimit(10)->setCollection($collection);
			$this->setChild('pager', $pager);// set pager block in layout
		}

		return $collection;
	}

	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
}
