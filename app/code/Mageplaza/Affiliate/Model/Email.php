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


use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface as PsrLogger;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Helper\Data as HelperData;

class Email
{
	/**
	 * Configuration paths for email identities
	 */
	const XML_PATH_AFFILIATE_EMAIL_IDENTITY = 'email/sender';

	/**
	 * @var PsrLogger
	 */
	protected $logger;

	/**
	 * @var ScopeConfigInterface
	 */
	private $scopeConfig;

	/**
	 * @var TransportBuilder
	 */
	private $transportBuilder;

	/**
	 * @var CustomerViewHelper
	 */
	protected $customerViewHelper;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	private $storeManager;

	protected $account;
	protected $helperData;

	public function __construct(
		CustomerViewHelper $customerViewHelper,
		ScopeConfigInterface $scopeConfig,
		StoreManagerInterface $storeManager,
		TransportBuilder $transportBuilder,
		PsrLogger $logger,
		AccountFactory $accountFactory,
		HelperData $helperData
	)
	{
		$this->helperData         = $helperData;
		$this->storeManager       = $storeManager;
		$this->customerViewHelper = $customerViewHelper;
		$this->scopeConfig        = $scopeConfig;
		$this->transportBuilder   = $transportBuilder;
		$this->logger             = $logger;
		$this->account            = $accountFactory;
	}


	/**
	 * Send corresponding email template
	 *
	 * @param string $template configuration path of email template
	 * @param string $sender configuration path of email identity
	 * @param array $templateParams
	 * @param int|null $storeId
	 * @return $this
	 */
	protected function sendEmailTemplate($emailTo, $nameTo, $template, $templateParams = [], $storeId = null)
	{
		$templateId = $this->scopeConfig->getValue($template, ScopeInterface::SCOPE_STORE, $storeId);
		if (!$storeId) {
			$storeId = \Magento\Store\Model\Store::DISTRO_STORE_ID;
		}
		$transport = $this->transportBuilder->setTemplateIdentifier($templateId)
			->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
			->setTemplateVars($templateParams)
			->setFrom($this->getSender())
			->addTo($emailTo, $nameTo)
			->getTransport();

		$transport->sendMessage();

		return $this;
	}

	public function getSender(){
		return $this->helperData->getAffiliateConfig(self::XML_PATH_AFFILIATE_EMAIL_IDENTITY);
	}


	public function sendRegisterEmail($customer, $websiteId = null, $redirectUrl = '')
	{
		if ($customer->getId()) {
			$account               = $this->account->create()->load($customer->getId(), 'customer_id');
			$storeId               = $this->getWebsiteStoreId($customer, null);
			$store                 = $this->storeManager->getStore($customer->getStoreId());
			$data['customername']  = $customer->getName();
			$data['store']         = $store;
			$data['accountstatus'] = $account->getStatus();
			$this->sendEmailTemplate(
				$customer->getEmail(),
				$customer->getName(),
				self::XML_PATH_REGISTER_AFFILIATE_EMAIL_TEMPLATE,
				self::XML_PATH_AFFILIATE_EMAIL_IDENTITY,
				$data,
				$storeId
			);
			try {
			} catch (MailException $e) {
				// If we are not able to send a new account email, this should be ignored
				$this->logger->critical($e);
			}
		}
	}

	protected function getWebsiteStoreId($customer, $defaultStoreId = null)
	{
		if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
			$storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
			reset($storeIds);
			$defaultStoreId = current($storeIds);
		}

		return $defaultStoreId;
	}

	public function sendReferEmail($friendName, $friendEmail, $params = [], $websiteId = null, $redirectUrl = '')
	{

		$store = $this->storeManager->getStore();
		$this->sendEmailTemplate(
			$friendEmail,
			$friendName,
			self::XML_PATH_REFER_AFFILIATE_EMAIL_TEMPLATE,
			self::XML_PATH_AFFILIATE_EMAIL_IDENTITY,
			$params
		);
		try {
		} catch (MailException $e) {
			// If we are not able to send a new account email, this should be ignored
			$this->logger->critical($e);
		}
	}

	public function updateBalanceEmail($customerName, $customerEmail, $params = [], $websiteId = null, $redirectUrl = '')
	{
		$store = $this->storeManager->getStore();
		$this->sendEmailTemplate(
			$customerEmail,
			$customerName,
			self::XML_PATH_UPDATE_BALANCE_AFFILIATE_EMAIL_TEMPLATE,
			self::XML_PATH_AFFILIATE_EMAIL_IDENTITY,
			$params
		);
		try {
		} catch (MailException $e) {
			// If we are not able to send a new account email, this should be ignored
			$this->logger->critical($e);
		}
	}

}
