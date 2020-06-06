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
 * Class Edit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Account
 */
class Edit extends \Mageplaza\Affiliate\Controller\Adminhtml\Account
{
	/**
	 * Result JSON factory
	 *
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $_resultJsonFactory;

	/**
	 * Constructor
	 *
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	)
	{
		$this->_resultJsonFactory = $resultJsonFactory;

		parent::__construct($context, $accountFactory, $coreRegistry, $resultPageFactory);
	}

	/**
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Mageplaza_Affiliate::account');
		$resultPage->getConfig()->getTitle()->set(__('Accounts'));

		/** @var \Mageplaza\Affiliate\Model\Account $account */
		$account = $this->_initAccount();

		$data = $this->_getSession()->getData('affiliate_account_data', true);
		if (!empty($data)) {
			$account->setData($data);
		}
		$this->_coreRegistry->register('current_account', $account);

		$title = $account->getId() ? __('Edit Account "%1"', $account->getId()) : __('New Account');
		$resultPage->getConfig()->getTitle()->prepend($title);

		return $resultPage;
	}
}
