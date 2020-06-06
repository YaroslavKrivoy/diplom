<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;

class Index extends \Mageplaza\Affiliate\Controller\Account
{
	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		$resultPage->getConfig()->getTitle()->set(__('My Credit'));

		return $resultPage;
	}
}
