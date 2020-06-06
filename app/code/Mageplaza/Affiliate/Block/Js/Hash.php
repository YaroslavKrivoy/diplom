<?php
namespace Mageplaza\Affiliate\Block\Js;


class Hash extends \Magento\Framework\View\Element\Template
{
	protected $_subscription;
	protected $_affiliateHelper;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Mageplaza\Affiliate\Helper\Data $affiliateHelper,
		array $data = []
	)
	{
		$this->_affiliateHelper = $affiliateHelper;
		parent::__construct($context, $data);
	}

	/**
	 * Get prefix
	 * @return mixed
	 */
	public function getPrefix()
	{
		return $this->_affiliateHelper->getAffiliateConfig('general/url/prefix');
	}

	/**
	 * Get cookie name
	 * @return string
	 */
	public function getCookieName()
	{
		return \Mageplaza\Affiliate\Helper\Data::AFFILIATE_COOKIE_NAME;
	}

	public function checkCookie()
	{
		if (!$this->_affiliateHelper->getAffiliateKeyFromCookie()) {
			return true;
		} else {
			if ($this->_affiliateHelper->getAffiliateConfig('general/overwrite_cookies')) {
				return true;
			}

			return false;
		}
	}

	public function getExpire()
	{
		$expireDay = (int)$this->_affiliateHelper->getAffiliateConfig('general/expired_time');

		return $expireDay * 24 * 3600;
	}
}
