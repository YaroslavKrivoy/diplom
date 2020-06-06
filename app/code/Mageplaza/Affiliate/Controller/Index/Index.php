<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Mageplaza\Affiliate\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;

		parent::__construct($context);
	}

	/**
	 * Default customer account page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();

		$resultPage->getConfig()->getTitle()->set(__('My Affiliate'));

		return $resultPage;
	}
}
