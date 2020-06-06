<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account\Withdraw;

/**
 * Class Withdrawpost
 * @package Mageplaza\Affiliate\Controller\Account
 */
class View extends \Mageplaza\Affiliate\Controller\Account
{
	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$id = $this->getRequest()->getParam('id');
		$withdraw = $this->withdrawFactory->create()->load($id);

		if(!$withdraw || !$withdraw->getId()){
			$this->messageManager->addErrorMessage(__('Cannot find item.'));
			$this->_redirect('*/account/withdraw');

			return;
		}

		$resultPage = $this->resultPageFactory->create();

		$this->registry->register('withdraw_view_data', $withdraw);

		return $resultPage;
	}
}
