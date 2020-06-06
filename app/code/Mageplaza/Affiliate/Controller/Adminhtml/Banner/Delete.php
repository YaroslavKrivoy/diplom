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
namespace Mageplaza\Affiliate\Controller\Adminhtml\Banner;

/**
 * Class Delete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Banner
 */
class Delete extends \Mageplaza\Affiliate\Controller\Adminhtml\Banner
{
	/**
	 * execute action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$id = $this->getRequest()->getParam('id');
		if ($id) {
			try {
				/** @var \Mageplaza\Affiliate\Model\Banner $banner */
				$banner = $this->_objectManager->create('Mageplaza\Affiliate\Model\Banner');
				$banner->load($id);
				$banner->delete();

				$this->messageManager->addSuccessMessage(__('The Banner has been deleted.'));
				$this->_redirect('affiliate/*/');

				return;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addErrorMessage(
					__('Something went wrong while deleting banner data. Please review the action log and try again.')
				);
				$this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);

				$this->_redirect('affiliate/*/edit', ['id' => $id]);

				return;
			}
		}

		$this->messageManager->addErrorMessage(__('We cannot find a banner to delete.'));
		$this->_redirect('affiliate/*/');
	}
}
