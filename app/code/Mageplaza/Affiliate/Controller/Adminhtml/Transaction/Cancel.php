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
 * Class Cancel
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class Cancel extends \Mageplaza\Affiliate\Controller\Adminhtml\Transaction
{
	/**
	 * execute the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		$transactionId  = $this->getRequest()->getParam('id');
		if ($transactionId) {
			$transaction = $this->_transactionFactory->create()->load($transactionId);
			if ($transaction->getId()) {
				try {
					$transaction->cancel();
					$this->messageManager->addSuccess(__('The Transaction has been canceled.'));
				} catch (\Exception $e) {
					$this->messageManager->addError($e->getMessage());
				}
			}
		}
		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}
}
