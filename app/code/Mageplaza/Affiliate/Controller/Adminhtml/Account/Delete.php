<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 *
 * @category  Mageplaza
 * @package   Mageplaza_Affiliate
 * @copyright Copyright (c) 2016
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Controller\Adminhtml\Account;

class Delete extends \Mageplaza\Affiliate\Controller\Adminhtml\Account
{
	/**
	 * execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		$id             = $this->getRequest()->getParam('id');
		if ($id) {
			try {
				/** @var \Mageplaza\Affiliate\Model\Account $account */
				$account = $this->_accountFactory->create();
				$account->load($id);
				$customer_id = $account->getCustomerId();
				$account->delete();
				$this->messageManager->addSuccess(__('The Account has been deleted.'));
				$this->_eventManager->dispatch('affiliate_account_delete_success', ['customer_id' => $customer_id]);

				$resultRedirect->setPath('affiliate/*/');

				return $resultRedirect;
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				// go back to edit form
				$resultRedirect->setPath('affiliate/*/edit', ['id' => $id]);

				return $resultRedirect;
			}
		}
		$this->messageManager->addError(__('Account to delete was not found.'));
		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}
}
