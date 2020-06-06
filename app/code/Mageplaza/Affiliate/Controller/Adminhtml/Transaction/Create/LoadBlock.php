<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Adminhtml\Transaction\Create;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;

/**
 * Class LoadBlock
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction\Create
 */
class LoadBlock extends \Mageplaza\Affiliate\Controller\Adminhtml\Transaction\Create
{
	/**
	 * @type \Magento\Framework\Controller\Result\RawFactory
	 */
	protected $resultRawFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Mageplaza\Affiliate\Model\TransactionFactory $transactionFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Mageplaza\Affiliate\Model\TransactionFactory $transactionFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		RawFactory $resultRawFactory
	)
	{
		$this->resultRawFactory = $resultRawFactory;

		parent::__construct($context, $transactionFactory, $coreRegistry, $resultPageFactory);
	}

	/**
	 * Loading page block
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
	 */
	public function execute()
	{
		$this->_initTransaction();

		$request = $this->getRequest();
		/** @var \Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();

		$block = $request->getParam('block');
		$resultPage->addHandle('affiliate_transaction_create_load_block_' . $block);

		if ($customerId = $request->getParam('customer_id')) {
			$this->_getSession()->setData('transaction_customer_id', $customerId);
		}

		$result = $resultPage->getLayout()->renderElement('content');

		return $this->resultRawFactory->create()->setContents($result);
	}
}
