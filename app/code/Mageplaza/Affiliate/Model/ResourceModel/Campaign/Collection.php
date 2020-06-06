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
namespace Mageplaza\Affiliate\Model\ResourceModel\Campaign;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * ID Field Name
	 *
	 * @var string
	 */
	protected $_idFieldName = 'campaign_id';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'mageplaza_affiliate_campaign_collection';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'campaign_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Affiliate\Model\Campaign', 'Mageplaza\Affiliate\Model\ResourceModel\Campaign');
	}

	/**
	 * Get SQL for get record count.
	 * Extra GROUP BY strip added.
	 *
	 * @return \Magento\Framework\DB\Select
	 */
	public function getSelectCountSql()
	{
		$countSelect = parent::getSelectCountSql();
		$countSelect->reset(\Zend_Db_Select::GROUP);

		return $countSelect;
	}

	/**
	 * @param string $valueField
	 * @param string $labelField
	 * @param array $additional
	 * @return array
	 */
	protected function _toOptionArray($valueField = 'campaign_id', $labelField = 'name', $additional = [])
	{
		return parent::_toOptionArray($valueField, $labelField, $additional);
	}

	public function getAvailableCampaign($affiliateGroupId, $websiteId, $filterDate = true, $customerGroupId = null)
	{
		$this->addFieldToFilter('website_ids', array('finset' => $websiteId))
			->addFieldToFilter('status', \Mageplaza\Affiliate\Model\Campaign\Status::ENABLED);

		if (!is_null($customerGroupId)) {
			$this->addFieldToFilter('customer_group_ids', array('finset' => (int)$customerGroupId));
		}

		if (!is_null($affiliateGroupId)) {
			$this->addFieldToFilter('affiliate_group_ids', array('finset' => (int)$affiliateGroupId));
		}

		if ($filterDate) {
			if ($filterDate === true) {
				$filterDate = date('Y-m-d');
			}
			$this->getSelect()->where('(from_date IS NULL) OR (date(from_date) <= date(?))', $filterDate);
			$this->getSelect()->where('(to_date IS NULL) OR (date(to_date) >= date(?))', $filterDate);
		}
		$this->setOrder('sort_order', self::SORT_ORDER_ASC);

		return $this;
	}
}
