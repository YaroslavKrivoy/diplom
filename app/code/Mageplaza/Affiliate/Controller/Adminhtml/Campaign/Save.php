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

class Save extends \Mageplaza\Affiliate\Controller\Adminhtml\Campaign
{
	/**
	 * Date filter
	 *
	 * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
	 */
	protected $_dateFilter;

	/**
	 * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
	 * @param \Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 * @param \Magento\Backend\App\Action\Context $context
	 */
	public function __construct(
		\Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
		\Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Backend\App\Action\Context $context
	)
	{
		$this->_dateFilter = $dateFilter;
		parent::__construct($context, $resultPageFactory, $registry, $campaignFactory);
	}

	/**
	 * run the action
	 *
	 * @return \Magento\Backend\Model\View\Result\Redirect
	 */
	public function execute()
	{
		$resultRedirect = $this->resultRedirectFactory->create();
		if ($data = $this->getRequest()->getPost('campaign')) {
			$data = $this->_filterData($data);

			$campaign = $this->_initCampaign();
			$campaign->loadPost($data);
			$this->_eventManager->dispatch('affiliate_campaign_prepare_save', [
				'campaign' => $campaign,
				'request'  => $this->getRequest()
			]);

			try {
				$campaign->save();
				$this->messageManager->addSuccessMessage(__('The Campaign has been saved successfully.'));
				$this->_getSession()->setData('affiliate_campaign_data', false);
				if ($this->getRequest()->getParam('back')) {
					$resultRedirect->setPath('affiliate/*/edit', ['id' => $campaign->getId()]);

					return $resultRedirect;
				}
				$resultRedirect->setPath('affiliate/*/');

				return $resultRedirect;
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addErrorMessage($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Campaign.'));
			}
			$this->_getSession()->setData('affiliate_campaign_data', $data);
			$resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

			return $resultRedirect;
		}
		$resultRedirect->setPath('affiliate/*/');

		return $resultRedirect;
	}

	/**
	 * filter values
	 *
	 * @param array $data
	 * @return array
	 */
	protected function _filterData($data)
	{
		$data = $this->getRequest()->getPostValue();
		if (isset($data['commission'])) {
			$data['commission'] = $this->correctTier($data['commission']);
		}

		$convertData = $data['campaign'];
		unset($data['campaign']);
		foreach ($convertData as $key => $value) {
			$data[$key] = $value;
		}

		/** Filter Date */
		$inputFilterDate = [];
		if (isset($data['from_date']) && $data['from_date']) $inputFilterDate['from_date'] = $this->_dateFilter;
		if (isset($data['to_date']) && $data['to_date']) $inputFilterDate['to_date'] = $this->_dateFilter;
		if (sizeof($inputFilterDate)) {
			$inputFilter = new \Zend_Filter_Input($inputFilterDate, [], $data);
			$data        = $inputFilter->getUnescaped();
		}

		if (isset($data['website_ids'])) {
			if (is_array($data['website_ids'])) {
				$data['website_ids'] = implode(',', $data['website_ids']);
			}
		}
		if (isset($data['affiliate_group_ids'])) {
			if (is_array($data['affiliate_group_ids'])) {
				$data['affiliate_group_ids'] = implode(',', $data['affiliate_group_ids']);
			}
		}
		if (isset($data['discount_action']) && $data['discount_action'] == 'by_percent' && isset($data['discount_amount'])
		) {
			$data['discount_amount'] = min(100, $data['discount_amount']);
		}
		if (isset($data['rule']['conditions'])) {
			$data['conditions'] = $data['rule']['conditions'];
		}
		if (isset($data['rule']['actions'])) {
			$data['actions'] = $data['rule']['actions'];
		}
		unset($data['rule']);

		return $data;
	}

	public function correctTier($data)
	{
		$correctData = array();
		$count       = 1;
		foreach ($data as $item) {
			$item['name']                  = __('Tier') . ' ' . $count;
			$correctData['tier_' . $count] = $item;
			$count++;
		}

		return $correctData;
	}
}
