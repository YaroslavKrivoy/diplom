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
namespace Mageplaza\Affiliate\Model\Banner;

class Campaign implements \Magento\Framework\Option\ArrayInterface
{
	const CAMPAIGN_COLLECTION = 1;
	protected $campaign;

	public function __construct(
		\Mageplaza\Affiliate\Model\CampaignFactory $campaignFactory
	)
	{
		$this->campaign = $campaignFactory;
	}

	protected function getCampaignCollection()
	{
		$campaignModel = $this->campaign->create();

		return $campaignModel->getCollection();
	}

	/**
	 * to option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$campaigns = $this->getCampaignCollection();
		$options   = array();
		foreach ($campaigns as $campaign) {
			$options[] = array('value' => $campaign->getId(), 'label' => $campaign->getName());
		}

		return $options;
	}
}
