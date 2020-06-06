<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mageplaza\Affiliate\Helper\Data as AffiliateHelper;

class OrderPlaceAfter implements ObserverInterface
{
	protected $_helper;

	public function __construct(
		AffiliateHelper $helper
	)
	{
		$this->_helper = $helper;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		if ($order->getAffiliateKey()) {
			$this->_helper->deleteAffiliateKeyFromCookie();
		}

		return $this;
	}
}
