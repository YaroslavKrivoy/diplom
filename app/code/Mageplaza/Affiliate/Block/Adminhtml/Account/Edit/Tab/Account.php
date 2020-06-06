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
namespace Mageplaza\Affiliate\Block\Adminhtml\Account\Edit\Tab;

/**
 * Class Account
 * @package Mageplaza\Affiliate\Block\Adminhtml\Account\Edit\Tab
 */
class Account extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * Country options
	 *
	 * @var \Magento\Config\Model\Config\Source\Yesno
	 */
	protected $_boolean;
	protected $_customerFactory;
	/**
	 * Status options
	 *
	 * @var \Mageplaza\Affiliate\Model\Account\Status
	 */
	protected $_status;

	/**
	 * Affiliate Group options
	 *
	 * @var \Mageplaza\Affiliate\Model\Account\Group
	 */
	protected $_group;
	protected $_accountFactory;
	protected $_pricingHelper;

	/**
	 * constructor
	 *
	 * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
	 * @param \Mageplaza\Affiliate\Model\Account\Status $statusOptions
	 * @param \Mageplaza\Affiliate\Model\Account\Group $affiliateGroupIdOptions
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Config\Model\Config\Source\Yesno $boolean,
		\Mageplaza\Affiliate\Model\Account\Status $status,
		\Mageplaza\Affiliate\Model\Account\Group $group,
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
		\Magento\Framework\Pricing\Helper\Data $pricingHelper,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	)
	{
		$this->_accountFactory  = $accountFactory;
		$this->_customerFactory = $customerFactory;
		$this->_pricingHelper = $pricingHelper;
		$this->_boolean         = $boolean;
		$this->_status          = $status;
		$this->_group           = $group;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		/** @var \Mageplaza\Affiliate\Model\Account $account */
		$account = $this->_coreRegistry->registry('current_account');
		$form    = $this->_formFactory->create();
		$form->setHtmlIdPrefix('account_');
		$form->setFieldNameSuffix('account');
		$fieldset = $form->addFieldset('base_fieldset', [
			'legend' => __('Account Information'),
			'class'  => 'fieldset-wide'
		]);

		$fieldset->addField('customer_id', 'hidden', ['name' => 'customer_id']);

		$customer = $this->_customerFactory->create()->load($account->getCustomerId());
		$fieldset->addField('customer_name', 'link', [
			'href'   => $this->getUrl('customer/index/edit', array('id' => $customer->getId())),
			'name'   => 'customer_name',
			'label'  => __('Customer'),
			'title'  => __('Customer'),
			'value'  => $customer->getName() . ' <' . $this->escapeHtml($customer->getEmail()) . '>',
			'target' => '_blank',
			'class'  => 'control-value',
			'style'  => 'text-decoration: none'
		]);

		$fieldset->addField('group_id', 'select', [
			'name'   => 'group_id',
			'label'  => __('Affiliate Group'),
			'title'  => __('Affiliate Group'),
			'values' => $this->_group->toOptionArray()
		]);

		$fieldset->addField('balance', 'note', [
				'label' => __('Balance'),
				'text'  => $this->_pricingHelper->currency($account->getBalance())
			]
		);

		$fieldset->addField('holding_balance', 'note', [
				'label' => __('Holding Balance'),
				'text'  => $this->_pricingHelper->currency($account->getHoldingBalance())
			]
		);

		if($account->getParent()) {
			$fieldset->addField('parent', 'hidden', ['name' => 'parent']);
			$parentAccount  = $this->_accountFactory->create()->load($account->getParent());
			$parentCustomer = $this->_customerFactory->create()->load($parentAccount->getCustomerId());
			$fieldset->addField('parent_account', 'link', [
				'href'   => $this->getUrl('affiliate/account/edit', array('id' => $account->getParent())),
				'name'   => 'parent_account',
				'label'  => __('Referred By'),
				'title'  => __('Referred By'),
				'value'  => $parentCustomer->getName() . ' <' . $this->escapeHtml($parentCustomer->getEmail()) . '>',
				'target' => '_blank',
				'class'  => 'control-value',
				'style'  => 'text-decoration: none'
			]);
		}

		$fieldset->addField('code', 'note', [
			'label' => __('Referral Code'),
			'text'  => $account->getCode(),
		]);

		$fieldset->addField('status', 'select', [
			'name'     => 'status',
			'label'    => __('Status'),
			'title'    => __('Status'),
			'required' => true,
			'values'   => $this->_status->toOptionArray(),
		]);

		$fieldset->addField('email_notification', 'select', [
			'name'   => 'email_notification',
			'label'  => __('Email Notification'),
			'title'  => __('Email Notification'),
			'values' => $this->_boolean->toOptionArray(),
		]);

		$form->addValues($account->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return __('Account');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->getTabLabel();
	}

	/**
	 * Can show tab in tabs
	 *
	 * @return boolean
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Tab is hidden
	 *
	 * @return boolean
	 */
	public function isHidden()
	{
		return false;
	}
}
