<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Mageplaza\Affiliate\Model\AccountFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Mageplaza\Affiliate\Model\WithdrawFactory;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Account extends Action
{
	protected $resultPageFactory;
	protected $transactionFactory;
	protected $accountFactory;
	protected $withdrawFactory;
	protected $dataHelper;
	protected $customerSession;
	protected $registry;

	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
		Context $context,
		PageFactory $resultPageFactory,
		TransactionFactory $transactionFactory,
		AccountFactory $accountFactory,
		WithdrawFactory $withdrawFactory,
		DataHelper $dataHelper,
		CustomerSession $customerSession,
		Registry $registry
	)
	{
		$this->resultPageFactory  = $resultPageFactory;
		$this->transactionFactory = $transactionFactory;
		$this->accountFactory     = $accountFactory;
		$this->withdrawFactory    = $withdrawFactory;
		$this->dataHelper         = $dataHelper;
		$this->customerSession    = $customerSession;
		$this->registry           = $registry;

		parent::__construct($context);
	}
}
