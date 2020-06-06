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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Index
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class Index extends \Mageplaza\Affiliate\Controller\Adminhtml\Campaign
{
	/**
	 * execute the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Mageplaza_Affiliate::campaign');
		$resultPage->getConfig()->getTitle()->prepend((__('Campaigns')));

		return $resultPage;
	}
}
