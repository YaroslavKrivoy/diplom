<?php
namespace Mageplaza\Affiliate\Block\Account\Withdraw;

class Transaction extends \Mageplaza\Affiliate\Block\Account\Withdraw
{
	public function getWithdraws()
	{
		$collection = $this->withdrawFactory->create()
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