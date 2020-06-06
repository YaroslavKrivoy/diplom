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

abstract class Group extends \Mageplaza\Affiliate\Controller\Adminhtml\AbstractAction
{
	/**
	 * Group Factory
	 *
	 * @var \Mageplaza\Affiliate\Model\GroupFactory
	 */
	protected $_groupFactory;

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Mageplaza\Affiliate\Model\GroupFactory $groupFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Affiliate\Model\GroupFactory $groupFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		$this->_groupFactory = $groupFactory;
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $resultPageFactory, $coreRegistry);
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	protected function _initGroup()
	{
		$groupId = (int)$this->getRequest()->getParam('id');
		/** @var \Mageplaza\Affiliate\Model\Group $account */
		$group = $this->_groupFactory->create();
		if ($groupId) {
			$group->load($groupId);
			if (!$group->getId()) {
				$this->messageManager->addError(__('This account no longer exists.'));
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('affiliate/account/index');

				return $resultRedirect;
			}
		}

		return $group;
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::group');
	}
}
