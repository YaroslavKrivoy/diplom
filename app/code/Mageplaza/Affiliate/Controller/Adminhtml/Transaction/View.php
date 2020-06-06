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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class View
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class View extends \Mageplaza\Affiliate\Controller\Adminhtml\Transaction
{
	/**
	 * Init Transaction
	 *
	 * @return \Mageplaza\Affiliate\Model\Transaction
	 */
	protected function _initTransaction()
	{
		$transactionId = (int)$this->getRequest()->getParam('id');
		/** @var \Mageplaza\Affiliate\Model\Transaction $transaction */
		$transaction = $this->_transactionFactory->create();
		if ($transactionId) {
			$transaction->load($transactionId);
			if ($transaction->getId()) {
				$this->_coreRegistry->register('current_transaction', $transaction);

				return $transaction;
			}
		}
		$this->messageManager->addError(__('This Transaction no longer exists.'));
		$resultRedirect = $this->_resultRedirectFactory->create();
		$resultRedirect->setPath('affiliate/transaction/index');

		return $resultRedirect;
	}

	/**
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$transaction = $this->_initTransaction();

		/** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Mageplaza_Affiliate::transaction');
		$resultPage->getConfig()->getTitle()->set(__('Transactions'));

		$title = __('View Transaction "%1"', $transaction->getId());
		$resultPage->getConfig()->getTitle()->prepend($title);

		return $resultPage;
	}
}
