<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Withdrawpost
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Withdrawpost extends \Mageplaza\Affiliate\Controller\Account
{
	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$account        = $this->dataHelper->getCurrentAffiliate();
		if (!$account || !$account->getId() || !$account->isActive()) {
			$this->messageManager->addNoticeMessage(__('An error occur. Please contact us.'));

			$this->_redirect('*/*');

			return;
		}
		$customer = $this->customerSession->getCustomer();

		$data                = $this->getRequest()->getPostValue();
		$data['customer_id'] = $customer->getId();
		$data['account_id']  = $account->getId();

		$this->customerSession->setWithdrawFormData($data);

		$withdraw = $this->withdrawFactory->create();
		$withdraw->addData($data)
			->setAccount($account);

		try {
			$this->checkWithdrawAmount($withdraw);

			$withdraw->save();

			$this->messageManager->addSuccessMessage(__('Your request has been sent successfully. We will review your request and inform you once it\'s approved!'));
			$this->customerSession->setWithdrawFormData(false);
		} catch (LocalizedException $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		} catch (\RuntimeException $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		} catch (\Exception $e) {
			$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the request.'));
		}

		$this->_redirect('*/*/withdraw');

		return;
	}

	public function checkWithdrawAmount($withdraw)
	{
		$minBalance = floatval($this->dataHelper->getAffiliateConfig('withdraw/minimum_balance'));
		if ($minBalance && $withdraw->getAccount()->getBalance() < $minBalance) {
			throw new LocalizedException(__('Your balance is not enough for request withdraw.'));
		}

		$min = floatval($this->dataHelper->getAffiliateConfig('withdraw/minimum'));
		if ($min && $withdraw->getAmount() < $min) {
			throw new LocalizedException(__('The withdraw amount have to equal or greater than %1', $this->dataHelper->formatPrice($min)));
		}

		$max = floatval($this->dataHelper->getAffiliateConfig('withdraw/maximum'));
		if ($max && $withdraw->getAmount() > $max) {
			throw new LocalizedException(__('The withdraw amount have to equal or less than %1', $this->dataHelper->formatPrice($max)));
		}

		return $this;
	}
}
