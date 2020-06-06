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
namespace Mageplaza\Affiliate\Model\ResourceModel\Transaction;

/**
 * Class Collection
 * @package Mageplaza\Affiliate\Model\ResourceModel\Transaction
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * ID Field Name
	 *
	 * @var string
	 */
	protected $_idFieldName = 'transaction_id';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'affiliate_transaction_collection';

	/**
	 * Event object
	 *
	 * @var string
	 */
	protected $_eventObject = 'transaction_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Affiliate\Model\Transaction', 'Mageplaza\Affiliate\Model\ResourceModel\Transaction');
	}

	public function getFieldTotal($field = 'amount')
	{
		$this->_renderFilters();

		$sumSelect = clone $this->getSelect();
		$sumSelect->reset(\Zend_Db_Select::ORDER);
		$sumSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
		$sumSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
		$sumSelect->reset(\Zend_Db_Select::COLUMNS);

		$sumSelect->columns("SUM(`$field`)");

		return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
	}
}
