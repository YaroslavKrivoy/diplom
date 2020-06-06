<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Affiliate\Helper\Data;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;

class CreditmemoSaveAfter implements ObserverInterface
{
	protected $_accountFactory;
	protected $_transactionFactory;
	protected $_helper;

	public function __construct(
		AccountFactory $accountFactory,
		TransactionFactory $transactionFactory,
		Data $data
	)
	{
		$this->_accountFactory     = $accountFactory;
		$this->_transactionFactory = $transactionFactory;
		$this->_helper             = $data;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if (!$this->_helper->getAffiliateConfig('commission/process/refund')) {
			return $this;
		}

		$creditmemo = $observer->getEvent()->getCreditmemo();

		$commission = $creditmemo->getAffiliateCommission();
		if (is_array($commission) && sizeof($commission)) {
			foreach ($commission as $id => $com) {
				$account = $this->_accountFactory->create()->load($id);
				if ($account->getId()) {
					$creditmemo->setCommissionAmount($com);
					try {
						$this->_transactionFactory->create()->createTransaction('order/refund', $account, $creditmemo);
					} catch (\Exception $e) {
					}
				}
			}
		}

		return $this;
	}
}
