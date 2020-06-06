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
 * Class Delete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class Delete extends \Mageplaza\Affiliate\Controller\Adminhtml\Campaign
{
	/**
	 * execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		$id             = $this->getRequest()->getParam('campaign_id');
		if ($id) {
			try {
				/** @var \Mageplaza\Affiliate\Model\Campaign $campaign */
				$campaign = $this->_campaignFactory->create();
				$campaign->load($id);
				$campaign->delete();

				$this->messageManager->addSuccess(__('The Campaign has been deleted.'));
				$resultRedirect->setPath('affiliate/*/');

				return $resultRedirect;
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());

				// go back to edit form
				$resultRedirect->setPath('affiliate/*/edit', ['id' => $id]);

				return $resultRedirect;
			}
		}
		// display error message
		$this->messageManager->addError(__('Campaign to delete was not found.'));

		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}
}
