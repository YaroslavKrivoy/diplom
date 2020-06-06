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

/**
 * Class Campaign
 * @package Mageplaza\Affiliate\Model
 */
class Campaign extends \Magento\Rule\Model\AbstractModel
{
	/**
	 * Cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'affiliate_campaign';

	/**
	 * Cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'affiliate_campaign';

	protected $_eventPrefix = 'affiliate_campaign';

	/**
	 * Store already validated addresses and validation results
	 *
	 * @var array
	 */
	protected $_validatedAddresses = [];

	/**
	 * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
	 */
	protected $_condCombineFactory;

	/**
	 * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
	 */
	protected $_condProdCombineFactory;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
		\Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineFactory,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_condCombineFactory = $condCombineFactory;
		$this->_condProdCombineFactory   = $condProdCombineFactory;
		parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Mageplaza\Affiliate\Model\ResourceModel\Campaign');
	}

	/**
	 * @return \Magento\SalesRule\Model\Rule\Condition\Combine
	 */
	public function getConditionsInstance()
	{
		return $this->_condCombineFactory->create();
	}

	/**
	 * Get rule condition product combine model instance
	 *
	 * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
	 */
	public function getActionsInstance()
	{
		return $this->_condProdCombineFactory->create();
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
}
