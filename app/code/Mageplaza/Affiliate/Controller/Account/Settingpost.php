<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

class Settingpost extends \Mageplaza\Affiliate\Controller\Account
{
	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$accountData    = $this->getRequest()->getParam('account');
		$account        = $this->dataHelper->getCurrentAffiliate();
		if (!$account || !$account->getId()) {
			$this->_redirect('*/*/');

			return;
		}

		$account->addData(array(
			'email_notification' => isset($accountData['email_notification']) ? $accountData['email_notification'] : 0
		));
		if (sizeof($accountData)) {
			$account->addData($accountData);
		}
		try {
			$account->save();
			$this->messageManager->addSuccessMessage(__('Saved successfully!'));
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		} catch (\RuntimeException $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		} catch (\Exception $e) {
			$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Account.'));
		}

		$this->_redirect('*/account/setting');

		return;
	}
}
