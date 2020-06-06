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
 * Class Transaction
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Transaction extends \Mageplaza\Affiliate\Controller\Adminhtml\AbstractAction
{
	/**
	 * @type \Mageplaza\Affiliate\Model\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Mageplaza\Affiliate\Model\TransactionFactory $transactionFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Affiliate\Model\TransactionFactory $transactionFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		$this->_transactionFactory = $transactionFactory;

		parent::__construct($context, $resultPageFactory, $coreRegistry);
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::transaction');
	}
}
