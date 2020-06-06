<?php
namespace Mageplaza\Affiliate\Model\Transaction\Action\Order;

use \Mageplaza\Affiliate\Model\Transaction\AbstractAction;
use \Mageplaza\Affiliate\Model\Transaction\Status;
use \Mageplaza\Affiliate\Model\Transaction\Type;

class Invoice extends AbstractAction
{
	public function getAmount()
	{
		$object = $this->getObject();
		$amount = $object->getCommissionAmount();

		if ($object instanceof \Magento\Sales\Model\Order) {
			$amount -= $this->transactionFactory->create()
				->getCollection()
				->addFieldToFilter('account_id', $this->getAccount()->getId())
				->addFieldToFilter('action', 'order/invoice')
				->addFieldToFilter('order_id', $object->getId())
				->getFieldTotal();
		}

		return max(0, $amount);
	}

	public function getType()
	{
		return Type::COMMISSION;
	}

	public function getStatus()
	{
		$holdDays = $this->getHoldDays();
		if ($holdDays && $holdDays > 0) {
			return Status::STATUS_HOLD;
		}

		return Status::STATUS_COMPLETED;
	}

	public function getTitle($transaction = null)
	{
		$param = is_null($transaction) ? '#' . $this->getOrder()->getIncrementId() : '#' . $transaction->getOrderIncrementId();

		return __('Get commission for order %1', $param);
	}

	public function prepareAction()
	{
		$order           = $this->getOrder();
		$transactionData = array(
			'order_id'           => $order->getId(),
			'order_increment_id' => $order->getIncrementId(),
			'store_id'           => $order->getStoreId(),
			'campaign'           => $order->getCampaignId()
		);

		$holdDays = $this->getHoldDays();
		if ($holdDays > 0) {
			$transactionData['holding_to'] = $this->getHoldingDate($holdDays);
		}

		return $transactionData;
	}

	public function getOrder()
	{
		$object = $this->getObject();
		if ($object instanceof \Magento\Sales\Model\Order\Invoice) {
			$order = $object->getOrder();
		} else {
			$order = $object;
		}

		return $order;
	}

	public function getAdditionContent()
	{
		$extraContent = $this->getExtraContent();
		$object       = $this->getObject();
		if ($object instanceof \Magento\Sales\Model\Order\Invoice) {
			$extraContent['invoice_increment_id'] = $object->getIncrementId();
		}

		return $this->jsonHelper->jsonEncode($extraContent);
	}
}