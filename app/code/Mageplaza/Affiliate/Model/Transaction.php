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
namespace Mageplaza\Affiliate\Model;

use \Magento\Framework\Exception\LocalizedException;
use \Mageplaza\Affiliate\Model\Transaction\Type;
use \Mageplaza\Affiliate\Model\Transaction\Status;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Model
 */
class Transaction extends \Magento\Framework\Model\AbstractModel
{
	const XML_PATH_EMAIL_ENABLE = 'email/transaction/enable';
	const XML_PATH_TRANSACTION_EMAIL_UPDATE_BALANCE_TEMPLATE = 'affiliate/email/transaction/update_balance';
	/**
	 * Config action name
	 *
	 * @var string
	 */
	const XML_CONFIG_ACTIONS = 'transaction';

	/**
	 * Cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'affiliate_transaction';

	/**
	 * Cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'affiliate_transaction';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'affiliate_transaction';

	/**
	 * Object Manager
	 *
	 * @type
	 */
	protected $objectManager;

	/**
	 * Store Manager
	 *
	 * @type
	 */
	private $storeManager;

	/**
	 * Json Helper
	 *
	 * @type
	 */
	protected $jsonHelper;

	/**
	 * Affiliate Helper Data
	 *
	 * @type
	 */
	protected $helper;

	/**
	 * Message Manager
	 *
	 * @type
	 */
	protected $messageManager;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\ObjectManagerInterface $objectmanager
	 * @param \Magento\Framework\Json\Helper\Data $json
	 * @param \Mageplaza\Affiliate\Helper\Data $helper
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Registry $registry,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Framework\Json\Helper\Data $json,
		\Mageplaza\Affiliate\Helper\Data $helper,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->storeManager   = $storeManager;
		$this->messageManager = $messageManager;
		$this->jsonHelper     = $json;
		$this->helper         = $helper;

		$this->objectManager = $objectmanager;
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Affiliate\Model\ResourceModel\Transaction');
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	/**
	 * Prepare transaction data
	 *
	 * @param $actionCode
	 * @param $account
	 * @param $object
	 * @param $extra
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	protected function prepareData($actionCode, $account, $object, $extra)
	{
		$actionData = $this->getActionModel($actionCode)
			->setData(array(
				'account'       => $account,
				'object'        => $object,
				'code'          => $actionCode,
				'extra_content' => $extra
			))->prepareTransaction();

		$this->setData($actionData);

		return $this;
	}

	/**
	 * Create Transaction
	 *
	 * @param $actionCode
	 * @param $account
	 * @param $object
	 * @param array $extra
	 * @return $this
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function createTransaction($actionCode, $account, $object, $extra = array())
	{
		if (!$account->getId()) {
			throw new LocalizedException(__('Affiliate account must be existed.'));
		}

		$this->prepareData($actionCode, $account, $object, $extra);

		/** Don't create transaction without amount */
		if (!$this->getAmount()) {
			throw new LocalizedException(__('Transaction amount cannot be zero.'));
		}

		/** If account is not enough and negative balance is not allow, Don't create transaction */
		if ($this->getAmount() < 0 && ($account->getBalance() + $this->getTotalAmountHold() + $this->getAmount() < 0)) {
			if (!$this->getConfig('account/balance/negative'))
				throw new LocalizedException(
					__('Account balance is not enough to create this transaction.')
				);
		}

		$dbTransaction = $this->objectManager->create('\Magento\Framework\DB\Transaction');
		if ($this->getData('status') == Status::STATUS_HOLD) {
			$account->setHoldingBalance($account->getHoldingBalance() + $this->getAmount());
		} else {
			/** status Complete */
			if ($this->getAmount() > 0) {
				$balanceLimit = (double)$this->getConfig('account/balance/limit');
				if ($balanceLimit > 0 && ($account->getBalance() + $account->getHoldingBalance() + $this->getAmount()) > $balanceLimit) {
					if ($balanceLimit > ($account->getBalance() + $account->getHoldingBalance())) {
						$this->setAmount($balanceLimit - ($account->getBalance() + $account->getHoldingBalance()));
						$account->setBalance($balanceLimit);
					} else {
						throw new LocalizedException(__('Account balance has been reached the limit. Please contact to store owner.'));
					}
				} else {
					$account->setBalance($account->getBalance() + $this->getAmount());
				}
			} else {
				if ($this->getTotalAmountHold()) {
					if (abs($this->getAmount()) > $this->getTotalAmountHold()) {
						$account->setHoldingBalance($account->getHoldingBalance() - $this->getTotalAmountHold());
						$account->setBalance($account->getBalance() + $this->getAmount() + $this->getTotalAmountHold());
					} else {
						$account->setHoldingBalance($account->getHoldingBalance() + $this->getAmount());
					}

					$this->setIsUpdateHoldingTransaction(true);
				} else {
					$account->setBalance($account->getBalance() + $this->getAmount());
				}
			}

			$dbTransaction->addCommitCallback([$this, 'sendUpdateBalanceEmail']);
		}

		if ($this->getType() == Type::COMMISSION) {
			$account->setTotalCommission($account->getTotalCommission() + $this->getAmount());
		} elseif ($this->getType() == Type::PAID) {
			$account->setTotalPaid($account->getTotalPaid() + abs($this->getAmount()));
		}

		$dbTransaction->addObject($account)
			->addObject($this)
			->addCommitCallback([$this, 'updateAmountUsed'])
			->save();

		return $this;
	}

	/**
	 * Cancel transaction
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function cancel()
	{
		if (!$this->getId() || $this->getStatus() == Status::STATUS_CANCELED) {
			throw new \Exception(
				__('Invalid transaction data for canceling.')
			);
		}

		$dbTransaction = $this->objectManager->create('\Magento\Framework\DB\Transaction');
		$account       = $this->getAffiliateAccount();
		$cancelAmount  = $this->getAmount() - $this->getAmountUsed();
		if ($this->getStatus() == Status::STATUS_HOLD) {
			$account->setHoldingBalance($account->getHoldingBalance() - $cancelAmount);
		} elseif ($this->getStatus() == Status::STATUS_COMPLETED) {
			$account->setBalance($account->getBalance() - $cancelAmount);
			if ($account->getBalance() < 0 && !$this->getConfig('account/balance/negative')) {
				throw new \Exception(
					__('Account balance is not enough for canceling.')
				);
			}
			$dbTransaction->addCommitCallback([$this, 'sendUpdateBalanceEmail']);
		}
		$this->setStatus(Status::STATUS_CANCELED);

		$dbTransaction->addObject($account)
			->addObject($this)
			->save();

		return $this;
	}

	/**
	 * Complete transaction
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function complete()
	{
		if (!$this->getId() || $this->getAmount() <= 0 || $this->getStatus() >= Status::STATUS_COMPLETED
		) {
			throw new \Exception(
				__('Invalid transaction data to complete.')
			);
		}

		$account        = $this->getAffiliateAccount();
		$completeAmount = $this->getAmount() - $this->getAmountUsed();
		if ($this->getStatus() == Status::STATUS_HOLD) {
			$account->setHoldingBalance($account->getHoldingBalance() - $completeAmount);
		}
		$balanceLimit = (double)$this->getConfig('account/balance/limit');
		if ($balanceLimit > 0 && $this->getAmount() > 0 && ($account->getBalance() + $completeAmount) > $balanceLimit) {
			if ($balanceLimit > $account->getBalance()) {
				$account->setBalance($balanceLimit);
			} else {
				$this->messageManager->addError(
					__('Maximum amount allowed in account balance is %1.', $balanceLimit)
				);

				return $this;
			}
		} else {
			$account->setBalance($account->getBalance() + $completeAmount);
		}
		$this->setStatus(Status::STATUS_COMPLETED);

		$this->objectManager->create('\Magento\Framework\DB\Transaction')
			->addObject($account)
			->addObject($this)
			->addCommitCallback([$this, 'sendUpdateBalanceEmail'])
			->save();


		return $this;
	}

	/**
	 * Get action model for transaction action
	 *
	 * @param $code
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getActionModel($code)
	{
		$actionClass = $this->getConfig(self::XML_CONFIG_ACTIONS . '/' . $code);
		if ($actionClass) {
			$action = $this->objectManager->create($actionClass);
			if (is_object($action) && ($action instanceof \Mageplaza\Affiliate\Model\Transaction\AbstractAction)) {
				return $action;
			}
		}

		throw new LocalizedException(__('Action model is invalid for %1', $code));
	}

	/**
	 * Get Config value
	 *
	 * @param $code
	 * @param null $storeId
	 * @return mixed
	 */
	public function getConfig($code, $storeId = null)
	{
		return $this->helper->getAffiliateConfig($code, $storeId);
	}

	/**
	 * Load Affiliate Account
	 *
	 * @return mixed
	 */
	public function getAffiliateAccount()
	{
		if (!$this->hasData('affiliate_account')) {
			$this->setData('affiliate_account',
				$this->objectManager->create('\Mageplaza\Affiliate\Model\Account')->load($this->getAccountId())
			);
		}

		return $this->getData('affiliate_account');
	}

	/**
	 * Load Customer
	 *
	 * @return mixed
	 */
	public function getCustomer()
	{
		return $this->objectManager->create('\Magento\Customer\Model\Customer')->load($this->getCustomerId());
	}

	/**
	 * Pricing Helper
	 *
	 * @return mixed
	 */
	public function getPricingHelper()
	{
		return $this->objectManager->create('\Magento\Framework\Pricing\Helper\Data');
	}

	/**
	 * Send email update balance
	 *
	 * @return $this
	 */
	public function sendUpdateBalanceEmail()
	{
		$account = $this->getAffiliateAccount();
		if (!$this->helper->allowSendEmail($account, self::XML_PATH_EMAIL_ENABLE)) {
			return $this;
		}

		$customer = $this->getCustomer();
		if (!$customer || !$customer->getId()) {
			return $this;
		}

		try {
			$this->helper->sendEmailTemplate(
				$customer,
				self::XML_PATH_TRANSACTION_EMAIL_UPDATE_BALANCE_TEMPLATE,
				['account' => $account, 'transaction' => $this]);
		} catch (\Exception $e) {
		}

		return $this;
	}

	public function getAmountFormated($store)
	{
		return $this->getPricingHelper()->currencyByStore($this->getAmount(), $store->getId(), true, false);
	}

	public function getStatusLabel()
	{
		$statusModel = $this->objectManager->create('\Mageplaza\Affiliate\Model\Transaction\Status');
		$statusHash  = $statusModel->getOptionHash();

		return $statusHash[$this->getStatus()];
	}

	/**
	 * Todo: Update amount used for transaction
	 *
	 * @return $this
	 */
	public function updateAmountUsed()
	{
		if ($this->getAmount() < 0) {
			$this->_getResource()->updateAmountUsed($this);
		}

		return $this;
	}
}
