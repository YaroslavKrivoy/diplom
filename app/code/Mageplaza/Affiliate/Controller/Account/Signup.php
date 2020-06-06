<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Signup extends \Mageplaza\Affiliate\Controller\Account
{
	public function execute()
	{
		$account = $this->dataHelper->getCurrentAffiliate();
		if ($account && $account->getId()) {
			if(!$account->isActive()){
				$this->messageManager->addNoticeMessage(__('Your account is not active. Please contact us.'));
			}
			$resultRedirect = $this->resultRedirectFactory->create();
			$resultRedirect->setPath('*/*');

			return $resultRedirect;
		}

		/** @var \Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setHeader('Login-Required', 'true');
		return $resultPage;
	}
}
