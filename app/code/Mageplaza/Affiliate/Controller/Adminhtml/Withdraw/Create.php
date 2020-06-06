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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Withdraw;

/**
 * Class Create
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class Create extends \Mageplaza\Affiliate\Controller\Adminhtml\Withdraw
{
	/**
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Mageplaza_Affiliate::withdraw');
		$resultPage->getConfig()->getTitle()->set(__('Withdraws'));

		$withdraw = $this->_initWithdraw();

		$data = $this->_getSession()->getData('affiliate_withdraw_data', true);
		if (!empty($data)) {
			$withdraw->setData($data);
		}
		$this->_coreRegistry->register('current_withdraw', $withdraw);

		$title = $withdraw->getId() ? __('Edit Withdraw "%1"', $withdraw->getId()) : __('New Withdraw');
		$resultPage->getConfig()->getTitle()->prepend($title);

		return $resultPage;
	}
}
