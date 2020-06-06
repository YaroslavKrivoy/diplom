<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Account\Cancel;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Withdrawpost
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Cancel extends \Mageplaza\Affiliate\Controller\Account
{
	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$id             = $this->getRequest()->getParam('id');
		$withdraw       = $this->withdrawFactory->create();
		$withdraw->load($id);

		try {
			$withdraw->cancel();

			$this->messageManager->addSuccessMessage(__('The withdraw has been canceled successfully.'));
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
}
