<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Signuppost
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Signuppost extends \Mageplaza\Affiliate\Controller\Account
{
	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$account        = $this->dataHelper->getCurrentAffiliate();
		if ($account && $account->getId()) {
			if (!$account->isActive()) {
				$this->messageManager->addNoticeMessage(__('Your account is not active. Please contact us.'));
			}
			$this->_redirect('*/*');

			return;
		}

		$data = $this->getRequest()->getPostValue();
		if (!isset($data['terms'])) {
			$this->messageManager->addErrorMessage(__('You have to agree with term and conditions.'));
			$this->_redirect('*/*');

			return;
		}

		$customer            = $this->customerSession->getCustomer();
		$data['customer_id'] = $customer->getId();
		$data['group_id']    = $this->dataHelper->getAffiliateConfig('account/sign_up/default_group');;
		if (isset($data['referred_by'])) {
			$data['parent'] = $this->dataHelper->getAffiliateByEmailOrCode(trim($data['referred_by']));
		}
		$data['status']             = $this->dataHelper->getAffiliateConfig('account/sign_up/admin_approved') ?
			\Mageplaza\Affiliate\Model\Account\Status::NEED_APPROVED :
			\Mageplaza\Affiliate\Model\Account\Status::ACTIVE;
		$data['email_notification'] = $this->dataHelper->getAffiliateConfig('account/sign_up/default_email_notification');

		$account->addData($data);


		try {
			$account->save();

			if ($account->getStatus() == \Mageplaza\Affiliate\Model\Account\Status::NEED_APPROVED) {
				$this->messageManager->addSuccessMessage(__('Congratulations! You have successfully registered. We will review your affiliate account and inform you once it\'s approved!'));
			} else {
				$this->messageManager->addSuccessMessage(__('Congratulations! You have successfully registered.'));
			}

			$this->_redirect('*/*');

			return;
		} catch (LocalizedException $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		} catch (\RuntimeException $e) {
			$this->messageManager->addErrorMessage($e->getMessage());
		} catch (\Exception $e) {
			$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Account.'));
		}

		$this->_redirect('*/*/signup');

		return;
	}
}
