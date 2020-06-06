<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Affiliate\Model\AccountFactory;

class PredispatchObserver implements ObserverInterface
{
	protected $_accountFactory;

	protected $_helper;

	protected $urlParam;

	public function __construct(
		\Mageplaza\Affiliate\Helper\Data $helper,
		\Mageplaza\Affiliate\Model\Config\Source\Urlparam $urlParam,
		AccountFactory $accountFactory
	)
	{
		$this->_accountFactory = $accountFactory;
		$this->_helper         = $helper;
		$this->urlParam        = $urlParam;
	}

	/**
	 * Check Captcha On User Login Page
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$urlPrefix = $this->_helper->getAffiliateConfig('general/url/prefix');
		$key       = $observer->getRequest()->getParam($urlPrefix);
		if ($key) {
			$account   = $this->_accountFactory->create();
			$urlParams = $this->urlParam->getOptionHash();
			foreach ($urlParams as $code => $label) {
				$account->load($key, $code);
				if ($account->getId()) {
					if ($account->isActive()) {
						$isSetCookie = false;
						if ($this->_helper->getAffiliateKeyFromCookie()) {
							if ($this->_helper->getAffiliateConfig('general/overwrite_cookies')) {
								$isSetCookie = true;
							}
						} else {
							$isSetCookie = true;
						}

						if ($isSetCookie)
							$this->setAffiliateKeyToCookie($observer->getRequest(), $account->getCode());
					}
					break;
				}
			}
		}

		return $this;
	}

	public function setAffiliateKeyToCookie($request, $code)
	{
		$this->_helper->setAffiliateKeyToCookie($code);

		if ($source = $request->getParam('source')) {
			$this->_helper->setAffiliateKeyToCookie($source, \Mageplaza\Affiliate\Helper\Data::AFFILIATE_COOKIE_SOURCE_NAME);
			if ($key = $request->getParam('key')) {
				$this->_helper->setAffiliateKeyToCookie($key, \Mageplaza\Affiliate\Helper\Data::AFFILIATE_COOKIE_SOURCE_VALUE);
			}
		}
	}
}
