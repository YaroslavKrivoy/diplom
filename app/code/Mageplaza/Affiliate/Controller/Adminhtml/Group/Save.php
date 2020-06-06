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

/**
 * Class Save
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Group
 */
class Save extends \Mageplaza\Affiliate\Controller\Adminhtml\Group
{
	/**
	 * run the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data = $this->getRequest()->getPost('group')) {
			$group = $this->_initGroup();
			$group->setData($data);

			$this->_eventManager->dispatch('affiliate_group_prepare_save', ['group' => $group, 'action' => $this]);
			try {
				$group->save();
				$this->messageManager->addSuccess(__('The Group has been created successfully.'));
				$this->_getSession()->setData('affiliate_group_data', false);

				$resultRedirect->setPath('affiliate/*/');

				return $resultRedirect;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the Group.'));
			}
			$this->_getSession()->setData('affiliate_group_data', $data);

			$resultRedirect->setPath('affiliate/*/create', ['_current' => true]);

			return $resultRedirect;
		}
		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}
}
