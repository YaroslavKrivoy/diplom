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
namespace Mageplaza\Affiliate\Controller\Adminhtml;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Account extends \Mageplaza\Affiliate\Controller\Adminhtml\AbstractAction
{
	/**
	 * @type \Mageplaza\Affiliate\Model\AccountFactory
	 */
	protected $_accountFactory;

	/**
	 * @param \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		$this->_accountFactory = $accountFactory;

		parent::__construct($context, $resultPageFactory, $coreRegistry);
	}

	/**
	 * Init Account
	 *
	 * @return \Mageplaza\Affiliate\Model\Account
	 */
	protected function _initAccount()
	{
		$accountId = (int)$this->getRequest()->getParam('id');
		/** @var \Mageplaza\Affiliate\Model\Account $account */
		$account = $this->_accountFactory->create();
		if ($accountId) {
			$account->load($accountId);
			if (!$account->getId()) {
				$this->messageManager->addError(__('This account no longer exists.'));
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('affiliate/account/index');

				return $resultRedirect;
			}
		}

		return $account;
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::account');
	}
}
