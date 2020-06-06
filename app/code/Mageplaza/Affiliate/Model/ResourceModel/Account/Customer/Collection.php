<?php
/**
 * Customer Grid Collection
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Model\ResourceModel\Account\Customer;

class Collection extends \Magento\Customer\Model\ResourceModel\Customer\Collection
{
	/**
	 * @return $this
	 */
	protected function _initSelect()
	{
		parent::_initSelect();
		$this->addNameToSelect()->addAttributeToSelect(
			'email'
		)->addAttributeToSelect(
			'created_at'
		)->joinAttribute(
			'billing_postcode',
			'customer_address/postcode',
			'default_billing',
			null,
			'left'
		)->joinAttribute(
			'billing_city',
			'customer_address/city',
			'default_billing',
			null,
			'left'
		)->joinAttribute(
			'billing_telephone',
			'customer_address/telephone',
			'default_billing',
			null,
			'left'
		)->joinAttribute(
			'billing_regione',
			'customer_address/region',
			'default_billing',
			null,
			'left'
		)->joinAttribute(
			'billing_country_id',
			'customer_address/country_id',
			'default_billing',
			null,
			'left'
		)->joinField(
			'store_name',
			'store',
			'name',
			'store_id=store_id',
			null,
			'left'
		)->joinField(
			'website_name',
			'store_website',
			'name',
			'website_id=website_id',
			null,
			'left'
		);

		$accountCollection = \Magento\Framework\App\ObjectManager::getInstance()
			->create('Mageplaza\Affiliate\Model\ResourceModel\Account\Collection');

		if($accountCollection->getSize()) {
			$this->addFieldToFilter('entity_id', array('nin' => $accountCollection->getColumnValues('customer_id')));
		}

		return $this;
	}
}
