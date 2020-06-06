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

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Affiliate\Model\Withdraw\Status;

/**
 * Class Withdraw
 * @package Mageplaza\Affiliate\Model
 */
class Withdraw extends \Magento\Framework\Model\AbstractModel
{
	const XML_PATH_EMAIL_ENABLE = 'email/withdraw/enable';
	const XML_PATH_WITHDRAW_EMAIL_COMPLETE_TEMPLATE = 'affiliate/email/withdraw/complete';
	/**
	 * Cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'affiliate_withdraw';

	/**
	 * Cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'affiliate_withdraw';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'affiliate_withdraw';
	protected $objectManager;
	private $storeManager;
	protected $messageManager;
	protected $_paymentHelper;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\App\Action\Context $contextAction,
		\Magento\Framework\Registry $registry,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Mageplaza\Affiliate\Helper\Payment $helper,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->storeManager   = $storeManager;
		$this->messageManager = $contextAction->getMessageManager();
		$this->_paymentHelper = $helper;

		$this->objectManager = $contextAction->getObjectManager();
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Affiliate\Model\ResourceModel\Withdraw');
	}

	public function afterLoad()
	{
		parent::afterLoad();

		$paymentDetail = $this->objectManager->create('Magento\Framework\Json\Helper\Data')
			->jsonDecode($this->getPaymentDetails());

		$this->addData($paymentDetail);

		return $this;
	}

	public function beforeSave()
	{
		parent::beforeSave();

		//set payment method detail
		$methodModel = $this->getPaymentModel();
		$this->setPaymentDetails($methodModel->getWithdrawInfoDetail());

		if (!$this->getStatus()) {
			$this->setStatus(\Mageplaza\Affiliate\Model\Withdraw\Status::PENDING);
		}

		if ($this->isObjectNew()) {
			$this->prepareData();
		}
	}

	public function getPaymentModel()
	{
		$paymentModel = $this->_paymentHelper->getMethodModel($this->getPaymentMethod());
		$paymentModel->setData('withdraw', $this);

		return $paymentModel;
	}

	public function afterSave()
	{
		parent::afterSave();

		if ($this->dataHasChangedFor('status') && ($this->getStatus() == Status::COMPLETE)) {
			$this->sendWithdrawCompleteEmail();
		}
	}

	public function prepareData()
	{
		$account = $this->_paymentHelper->getAffiliateAccount($this->getCustomerId(), 'customer_id');
		if ($account->getBalance() < $this->getAmount()) {
			throw new LocalizedException(__('Account balance is not enough to create this transaction.'));
		}

		$this->setAccount($account)
			->setAccountId($account->getId());

		if (!$this->getFee()) {
			$this->setFee($this->_paymentHelper->getFee($this->getPaymentMethod(), $this->getAmount()));
		}

		$transferAmount = $this->getAmount() - $this->getFee();
		if ($transferAmount <= 0) {
			throw new LocalizedException(__('The amount request is not enough to pay for fee.'));
		}

		$this->setTransferAmount($transferAmount);

		if (!$this->getTransactionId()) {
			$transaction = $this->objectManager->create('Mageplaza\Affiliate\Model\Transaction')
				->createTransaction('withdraw/create', $this->getAccount(), $this);

			if (!$transaction || !$transaction->getId()) {
				throw new LocalizedException(__('Cannot create transaction for this withdraw.'));
			}

			$this->setTransactionId($transaction->getId());
		}


		return $this;
	}

	public function cancel()
	{
		if (!$this->getId() || $this->getStatus() == Status::CANCEL) {
			throw new \Exception(
				__('Invalid withdraw data for canceling.')
			);
		}

		$transaction = $this->objectManager->create('Mageplaza\Affiliate\Model\Transaction')
			->load($this->getTransactionId());
		$transaction->cancel();

		$this->setStatus(Status::CANCEL)
			->save();

		return $this;
	}

	public function approve()
	{
		if (!$this->getId() || $this->getStatus() >= Status::COMPLETE) {
			throw new \Exception(
				__('Invalid withdraw data for approved.')
			);
		}

		$this->setStatus(Status::COMPLETE)
			->save();

		return $this;
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

	public function getStatusLabel($status = null)
	{
		if ($status == null) {
			$status = $this->getStatus();
		}
		$statusHash = $this->objectManager->create('\Mageplaza\Affiliate\Model\Withdraw\Status')->getOptionHash();

		return $statusHash[$status];
	}

	public function getPaymentLabel($payment = null)
	{
		if ($payment == null) {
			$payment = $this->getPaymentMethod();
		}

		$payments = $this->_paymentHelper->getAllMethods();

		if (isset($payments[$payment])) {
			return __($payments[$payment]['label']);
		}

		return $payment;
	}

	public function canCancel()
	{
		return $this->getStatus() == Status::PENDING;
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

	public function getPricingHelper()
	{
		return $this->objectManager->create('\Magento\Framework\Pricing\Helper\Data');
	}

	public function getAmountFormated($store)
	{
		return $this->getPricingHelper()->currencyByStore($this->getAmount(), $store->getId(), true, false);
	}

	public function getFeeAmountFormated($store)
	{
		return $this->getPricingHelper()->currencyByStore($this->getFee(), $store->getId(), true, false);
	}

	public function getTransferAmountFormated($store)
	{
		return $this->getPricingHelper()->currencyByStore($this->getTransferAmount(), $store->getId(), true, false);
	}

	public function sendWithdrawCompleteEmail()
	{
		$account = $this->getAffiliateAccount();
		if (!$this->_paymentHelper->allowSendEmail($account, self::XML_PATH_EMAIL_ENABLE)) {
			return $this;
		}

		$customer = $this->getCustomer();
		if (!$customer || !$customer->getId()) {
			return $this;
		}

		try {
			$this->_paymentHelper->sendEmailTemplate(
				$customer,
				self::XML_PATH_WITHDRAW_EMAIL_COMPLETE_TEMPLATE,
				['account' => $account, 'withdraw' => $this]);
		} catch (\Exception $e) {
		}

		return $this;
	}
}
