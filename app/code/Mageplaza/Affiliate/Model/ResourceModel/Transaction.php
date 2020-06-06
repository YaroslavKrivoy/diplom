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
namespace Mageplaza\Affiliate\Model\ResourceModel;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Model\ResourceModel
 */
class Transaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('mageplaza_affiliate_transaction', 'transaction_id');
	}

	/**
	 * before save callback
	 *
	 * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\Affiliate\Model\Transaction $object
	 * @return $this
	 */
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
	{
		$object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
		if ($object->isObjectNew()) {
			$object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
		}

		return parent::_beforeSave($object);
	}

	/**
	 * Update amount used for complete and onhold transaction
	 *
	 * @param $transaction
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function updateAmountUsed($transaction, $totalAmount = null)
	{
		if (is_null($totalAmount))
			$totalAmount = -$transaction->getAmount();

		if ($totalAmount <= 0) {
			return $this;
		}

		$adapter = $this->getConnection();
		$select  = $adapter->select()
			->from($this->getMainTable(), ['transaction_id', 'amount', 'amount_used'])
			->where('customer_id = :customer_id')
			->where('status = :status')
			->where('amount > amount_used');

		$binds = ['customer_id' => (int)$transaction->getCustomerId(), 'status' => \Mageplaza\Affiliate\Model\Transaction\Status::STATUS_COMPLETED];

		if ($transaction->getIsUpdateHoldingTransaction()) {
			$select->where('order_id = :order_id');
			$binds['order_id'] = (int)$transaction->getOrderId();
			$binds['status']   = \Mageplaza\Affiliate\Model\Transaction\Status::STATUS_HOLD;
		}

		$trans = $adapter->fetchAll($select, $binds);

		if (empty($trans) || !is_array($trans)) {
			return $this;
		}
		$usedIds = array();
		$lastId  = 0;
		$lastUse = 0;
		foreach ($trans as $tran) {
			$availableAmount = $tran['amount'] - $tran['amount_used'];
			if ($totalAmount < $availableAmount) {
				$lastUse     = $tran['amount_used'] + $totalAmount;
				$lastId      = $tran['transaction_id'];
				$totalAmount = 0;
				break;
			}
			$totalAmount -= $availableAmount;
			$usedIds[] = $tran['transaction_id'];
			if ($totalAmount == 0) {
				break;
			}
		}

		// Update all depend transactions
		if (count($usedIds)) {
			$this->getConnection()->update(
				$this->getMainTable(),
				['amount_used' => new \Zend_Db_Expr('amount'), 'status' => \Mageplaza\Affiliate\Model\Transaction\Status::STATUS_COMPLETED],
				[new \Zend_Db_Expr('transaction_id IN ( ' . implode(' , ', $usedIds) . ' )')]
			);
		}
		if ($lastId) {
			$this->getConnection()->update(
				$this->getMainTable(),
				['amount_used' => new \Zend_Db_Expr((string)$lastUse)],
				['transaction_id = ?' => (int)$lastId]
			);
		}

		if ($transaction->getIsUpdateHoldingTransaction() && $totalAmount) {
			$transaction->setIsUpdateHoldingTransaction(false);
			$this->updateAmountUsed($transaction, $totalAmount);
		}

		return $this;
	}

}
