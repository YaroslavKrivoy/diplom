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

/**
 * Class Create
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Account
 */
class Create extends \Mageplaza\Affiliate\Controller\Adminhtml\Account
{
	/**
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Mageplaza_Affiliate::account');
		$resultPage->getConfig()->getTitle()->set(__('Account'));

		$account = $this->_initAccount();
		$data    = $this->_getSession()->getData('affiliate_account_data', true);
		if (!empty($data)) {
			$account->setData($data);
		}
		$this->_coreRegistry->register('current_account', $account);

		$resultPage->getConfig()->getTitle()->prepend(__('New Account'));

		return $resultPage;
	}
}
