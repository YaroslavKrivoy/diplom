<?php

namespace Mageplaza\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesConvertQuote implements ObserverInterface
{

	protected $account;
	protected $customer;

	public function __construct(
		\Mageplaza\Affiliate\Model\AccountFactory $account,
        \Magento\Customer\Model\CustomerFactory $customer
	) {
		$this->account = $account;
		$this->customer = $customer;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		$quote = $observer->getEvent()->getQuote();

		if($quote->getAffiliateKey()) {
			$email = '';
			$referrer = $this->account->create()->loadByCode($quote->getAffiliateKey());
            if($referrer->getId()){
                $customer = $this->customer->create()->load($referrer->getCustomerId());
                if($customer->getId()){
                    $email = $customer->getEmail();
                }
            }



			$order->setReferralEmail($email)->setAffiliateKey($quote->getAffiliateKey())
				->setAffiliateCommission($quote->getAffiliateCommission())
				->setTotalAffiliateCommission($quote->getTotalAffiliateCommission())
				->setAffiliateDiscountAmount($quote->getAffiliateDiscountAmount())
				->setBaseAffiliateDiscountAmount($quote->getBaseAffiliateDiscountAmount());
		}

		return $this;
	}
}
