<?php
namespace Mageplaza\Affiliate\Model\Transaction;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Mageplaza\Affiliate\Model\TransactionFactory;

abstract class AbstractAction extends \Magento\Framework\DataObject
{
	protected $jsonHelper;
	protected $storeManager;
	protected $dataHelper;
	protected $transactionFactory;

	public function __construct(
		JsonHelper $jsonHelper,
		StoreManagerInterface $storeManager,
		DataHelper $dataHelper,
		TransactionFactory $transactionFactory,
		array $data = []
	)
	{
		$this->jsonHelper         = $jsonHelper;
		$this->storeManager       = $storeManager;
		$this->dataHelper         = $dataHelper;
		$this->transactionFactory = $transactionFactory;
		parent::__construct($data);
	}

	abstract public function getAmount();

	abstract public function getTitle($transaction = null);

	abstract public function getType();

	/**
	 * Prepare transaction data
	 *
	 * @return array
	 */
	public function prepareTransaction()
	{
		$defaultData = array(
			'account_id'      => $this->getAccount()->getId(),
			'customer_id'     => $this->getAccount()->getCustomerId(),
			'action'          => $this->getCode(),
			'type'            => $this->getType(),
			'amount'          => $this->getAmount(),
			'current_balance' => $this->getAccount()->getBalance(),
			'status'          => $this->getStatus(),
			'store_id'        => $this->storeManager->getStore()->getId(),
			'title'           => $this->getTitle(),
			'extra_content'   => $this->getAdditionContent()
		);

		return array_merge($defaultData, $this->prepareAction());
	}

	public function prepareAction()
	{
		return [];
	}

	public function getStatus()
	{
		return \Mageplaza\Affiliate\Model\Transaction\Status::STATUS_COMPLETED;
	}

	/**
	 * Get extra content of transaction
	 *
	 * @return string
	 */
	public function getAdditionContent()
	{
		$extraContent = $this->getExtraContent();
		if (!is_array($extraContent)) {
			return null;
		}

		return $this->jsonHelper->jsonEncode($extraContent);
	}

	/**
	 * Holding date of transaction. Transaction will be completed when holding date is reached
	 *
	 * @return date
	 */
	public function getHoldDays()
	{
		$holdDays = (int)$this->dataHelper->getAffiliateConfig(
			'commission/process/holding_days',
			$this->getOrder()->getStoreId()
		);

		return $holdDays;
	}

	public function getHoldingDate($days)
	{
		if ($days <= 0) {
			return null;
		}

		$holdDate = new \Zend_Date();
		$holdDate->addDay(abs((int)$days));

		return $holdDate->toString();
	}
}