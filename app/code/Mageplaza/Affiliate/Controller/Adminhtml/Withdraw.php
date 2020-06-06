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

abstract class Withdraw extends \Mageplaza\Affiliate\Controller\Adminhtml\AbstractAction
{
	/**
	 * Withdraw Factory
	 *
	 * @var \Mageplaza\Affiliate\Model\WithdrawFactory
	 */
	protected $_withdrawFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Mageplaza\Affiliate\Model\WithdrawFactory $withdrawFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Mageplaza\Affiliate\Model\WithdrawFactory $withdrawFactory
	)
	{
		$this->_withdrawFactory = $withdrawFactory;
		parent::__construct($context, $resultPageFactory, $coreRegistry);
	}

	/**
	 * Init Withdraw
	 *
	 * @return \Mageplaza\Affiliate\Model\Withdraw
	 */
	protected function _initWithdraw()
	{
		$withdrawId = (int)$this->getRequest()->getParam('id');
		/** @var \Mageplaza\Affiliate\Model\Account $account */
		$withdraw = $this->_withdrawFactory->create();
		if ($withdrawId) {
			$withdraw->load($withdrawId);
			if (!$withdraw->getId()) {
				$this->messageManager->addErrorMessage(__('This withdrawal no longer exists.'));
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('affiliate/withdraw/index');

				return $resultRedirect;
			}
		}

		return $withdraw;
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::withdraw');
	}
}
