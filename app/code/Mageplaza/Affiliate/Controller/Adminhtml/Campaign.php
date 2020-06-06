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
 * Class Campaign
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Campaign extends \Mageplaza\Affiliate\Controller\Adminhtml\AbstractAction
{
	/**
	 * @type \Mageplaza\Affiliate\Model\CampaignFactory
	 */
	protected $_campaignFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory
	)
	{
		$this->_campaignFactory = $campaignFactory;

		parent::__construct($context, $resultPageFactory, $coreRegistry);
	}

	/**
	 * Init Campaign
	 *
	 * @return \Mageplaza\Affiliate\Model\Campaign
	 */
	protected function _initCampaign()
	{
		$campaignId = (int)$this->getRequest()->getParam('id');
		/** @var \Mageplaza\Affiliate\Model\Account $account */
		$campaign = $this->_campaignFactory->create();
		if ($campaignId) {
			$campaign->load($campaignId);
			if (!$campaign->getId()) {
				$this->messageManager->addError(__('This campaign no longer exists.'));
				$resultRedirect = $this->resultRedirectFactory->create();
				$resultRedirect->setPath('affiliate/campaign/index');

				return $resultRedirect;
			}
		}

		return $campaign;
	}

	/**
	 * is action allowed
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Mageplaza_Affiliate::campaign');
	}
}
