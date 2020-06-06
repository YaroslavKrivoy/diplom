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
namespace Mageplaza\Affiliate\Block\Adminhtml\Account\Create\Tab;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction\Create\Tab
 */
class Info extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * @type \Magento\Customer\Model\CustomerFactory
	 */
	protected $customerFactory;
	protected $_status;
	protected $_boolean;
	protected $_group;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Mageplaza\Affiliate\Model\AccountFactory $accountFactory
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Config\Model\Config\Source\Yesno $booleanOptions,
		\Mageplaza\Affiliate\Model\Account\Status $statusOptions,
		\Mageplaza\Affiliate\Model\Account\Group $group,
		array $data = []
	)
	{
		$this->customerFactory = $customerFactory;
		$this->_boolean        = $booleanOptions;
		$this->_status         = $statusOptions;
		$this->_group          = $group;
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
		$form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('account_');
		$form->setFieldNameSuffix('account');
		$fieldset = $form->addFieldset('base_fieldset', [
			'legend' => __('Account Information'),
			'class'  => 'fieldset-wide'
		]);
		$fieldset->addField('customer_id', 'hidden', [
			'name' => 'customer_id'
		]);

		$fieldset->addField('customer_name', 'text', [
			'label'    => __('Customer'),
			'name'     => 'customer_name',
			'readonly' => true
		]);

		$fieldset->addField('group_id', 'select', [
			'name'     => 'group',
			'label'    => __('Affiliate Group'),
			'title'    => __('Affiliate Group'),
			'required' => true,
			'values'   => $this->_group->toOptionArray()
		]);

		$fieldset->addField('parent', 'text', [
			'name'  => 'parent',
			'label' => __('Referred By'),
			'title' => __('Referred By'),
			'class' => 'validate-number',
			'note'  => __('Affiliate account Id')
		]);
		$fieldset->addField('status', 'select', [
			'name'     => 'status',
			'label'    => __('Status'),
			'title'    => __('Status'),
			'required' => true,
			'values'   => $this->_status->toOptionArray()
		]);
		$fieldset->addField('email_notification', 'select', [
			'name'   => 'email_notification',
			'label'  => __('Email Notification'),
			'title'  => __('Email Notification'),
			'values' => $this->_boolean->toOptionArray(),
		]);


		$form->addValues($this->prepareAccountData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * @return mixed
	 */
	public function prepareAccountData()
	{
		$account = $this->_coreRegistry->registry('current_account');
		if ($customerId = $this->_backendSession->getData('account_customer_id')) {
			$customer = $this->customerFactory->create();
			$customer->load($customerId);
			$account->setCustomerId($customerId)
				->setCustomerName($customer->getName() . ' <' . $customer->getEmail() . '>')
				->setEmailNotification(1);
		}

		return $account->getData();
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
