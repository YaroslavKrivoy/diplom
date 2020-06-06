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

use Mageplaza\Affiliate\Helper\Data as DataHelper;
use Magento\Customer\Model\CustomerFactory;
use Mageplaza\Affiliate\Model\Account\Status;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Model
 */
class Account extends \Magento\Framework\Model\AbstractModel
{
	const XML_PATH_EMAIL_ENABLE = 'email/account/enable';
	const XML_PATH_ACCOUNT_EMAIL_WELCOME_TEMPLATE = 'affiliate/email/account/welcome';
	const XML_PATH_ACCOUNT_EMAIL_APPROVE_TEMPLATE = 'affiliate/email/account/approve';
	/**
	 * Cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'affiliate_account';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'affiliate_account';

	/**
	 * @type \Mageplaza\Affiliate\Helper\Data
	 */
	protected $_helper;

	/**
	 * @type \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * Object Manager
	 *
	 * @type
	 */
	protected $objectManager;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		DataHelper $helper,
		CustomerFactory $customerFactory,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_helper          = $helper;
		$this->_customerFactory = $customerFactory;
		$this->objectManager    = $objectmanager;

		parent::__construct($context, $registry, $resource, $resourceCollection);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Affiliate\Model\ResourceModel\Account');
	}

	public function afterSave()
	{
		parent::afterSave();

		if ($this->isObjectNew()) {
			$this->sendWelcomeEmail();
		}

		if ($this->hasDataChanges() &&
			$this->getOrigData('status') == Status::NEED_APPROVED &&
			$this->getData('status') == Status::ACTIVE
		) {
			$this->sendApproveEmail();
		}
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
	 * @param $code
	 * @return $this
	 */
	public function loadByCode($code)
	{
		return $this->load($code, 'code');
	}

	/**
	 * @param $customer
	 * @return $this
	 */
	public function loadByCustomer($customer)
	{
		return $this->loadByCustomerId($customer->getId());
	}

	/**
	 * @param $customerId
	 * @return $this
	 */
	public function loadByCustomerId($customerId)
	{
		return $this->load($customerId, 'customer_id');
	}

	public function getCustomer()
	{
		$customer = $this->_customerFactory->create()->load($this->getCustomerId());

		return $customer;
	}

	public function getPricingHelper()
	{
		return $this->objectManager->create('\Magento\Framework\Pricing\Helper\Data');
	}

	public function getBalanceFormated($store)
	{
		return $this->getPricingHelper()->currencyByStore($this->getBalance(), $store->getId(), true, false);
	}

	/**
	 * @return bool
	 */
	public function isActive()
	{
		return $this->getStatus() == \Mageplaza\Affiliate\Model\Account\Status::ACTIVE;
	}

	public function sendWelcomeEmail()
	{
		$this->_sendEmail(self::XML_PATH_ACCOUNT_EMAIL_WELCOME_TEMPLATE);
	}

	public function sendApproveEmail()
	{
		$this->_sendEmail(self::XML_PATH_ACCOUNT_EMAIL_APPROVE_TEMPLATE);
	}

	protected function _sendEmail($template)
	{
		if (!$this->_helper->allowSendEmail($this, self::XML_PATH_EMAIL_ENABLE)) {
			return $this;
		}

		$customer = $this->getCustomer();
		if (!$customer || !$customer->getId()) {
			return $this;
		}

		try {
			$this->_helper->sendEmailTemplate($customer, $template, ['account' => $this]);
		} catch (\Exception $e) {
		}

		return $this;
	}
}
