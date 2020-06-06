<?php
namespace Mageplaza\Affiliate\Model\Transaction\Action\Order;

use Mageplaza\Affiliate\Model\Transaction\Status;
use \Mageplaza\Affiliate\Model\Transaction\Type;

/**
 * Class Refund
 * @package Mageplaza\Affiliate\Model\Transaction\Action\Order
 */
class Refund extends \Mageplaza\Affiliate\Model\Transaction\AbstractAction
{
	/**
	 * @return float
	 */
	public function getAmount()
	{
		return -(float)$this->getObject()->getCommissionAmount();
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return Type::COMMISSION;
	}

	/**
	 * @param null $transaction
	 * @return \Magento\Framework\Phrase
	 */
	public function getTitle($transaction = null)
	{
		$param = is_null($transaction) ? '#' . $this->getOrder()->getIncrementId() : '#' . $transaction->getOrderIncrementId();

		return __('Taken back commission for refunding order %1', $param);
	}

	/**
	 * @return array
	 */
	public function prepareAction()
	{
		$order = $this->getOrder();

		$totalAmountHold = $this->transactionFactory->create()
			->getCollection()
			->addFieldToFilter('order_id', $order->getId())
			->addFieldToFilter('status', Status::STATUS_HOLD)
			->getFieldTotal();

		$transactionData = array(
			'order_id'           => $order->getId(),
			'order_increment_id' => $order->getIncrementId(),
			'store_id'           => $order->getStoreId(),
			'campaign'           => $order->getCampaignId(),
			'total_amount_hold'  => $totalAmountHold
		);


		return $transactionData;
	}

	/**
	 * @return \Magento\Sales\Model\Order
	 */
	public function getOrder()
	{
		$object = $this->getObject();
		if ($object instanceof \Magento\Sales\Model\Order\Creditmemo) {
			$order = $object->getOrder();
		} else {
			$order = $object;
		}

		return $order;
	}

	/**
	 * @return string
	 */
	public function getAdditionContent()
	{
		$extraContent = $this->getExtraContent();
		$object       = $this->getObject();
		if ($object instanceof \Magento\Sales\Model\Order\Creditmemo) {
			$extraContent['creditmemo_increment_id'] = $object->getIncrementId();
		}

		return $this->jsonHelper->jsonEncode($extraContent);
	}
}