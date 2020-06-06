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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Group;

class Create extends \Mageplaza\Affiliate\Controller\Adminhtml\Group
{
	/**
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		/** @var \Mageplaza\Affiliate\Model\Group $group */
		$group = $this->_initGroup();
		$data  = $this->_getSession()->getData('affiliate_group_data', true);
		if (!empty($data)) {
			$group->setData($data);
		}
		$this->_coreRegistry->register('current_group', $group);

		/** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
		$resultPage = $this->_resultPageFactory->create();
		$resultPage->setActiveMenu('Mageplaza_Affiliate::group');
		$resultPage->getConfig()->getTitle()->set(__('Groups'));

		$resultPage->getConfig()->getTitle()->prepend(__('New Group'));

		return $resultPage;
	}
}
