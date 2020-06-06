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
namespace Mageplaza\Affiliate\Block\Adminhtml\Transaction\Create\Tab;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction\Create\Tab
 */
class Transaction extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * @type \Mageplaza\Affiliate\Model\AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @type \Magento\Customer\Model\CustomerFactory
	 */
	protected $customerFactory;

	/**
	 * @type \Magento\Framework\Pricing\Helper\Data
	 */
	protected $pricingHelper;

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
		\Mageplaza\Affiliate\Model\AccountFactory $accountFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Framework\Pricing\Helper\Data $pricingHelper,
		array $data = []
	)
	{
		$this->pricingHelper   = $pricingHelper;
		$this->customerFactory = $customerFactory;
		$this->accountFactory  = $accountFactory;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		/** @var \Mageplaza\Affiliate\Model\Transaction $transaction */
		$form     = $this->_formFactory->create();
		$form->setHtmlIdPrefix('transaction_');
		$form->setFieldNameSuffix('transaction');

		$fieldset = $form->addFieldset('base_fieldset', [
				'legend' => __('Transaction Information'),
				'class'  => 'fieldset-wide'
			]
		);

		$fieldset->addField('customer_id', 'hidden', [
				'name' => 'customer_id'
			]
		);
		$fieldset->addField('customer_name', 'text', [
				'label'    => __('Account'),
				'name'     => 'customer_name',
				'readonly' => true
			]
		);
		$fieldset->addField('amount', 'text', [
				'label'    => __('Amount'),
				'name'     => 'amount',
				'required' => true,
				'class'    => 'validate-number',
				'note'     => __('Add or subtract affiliate\'s balance. E.g: 99 or -99')
			]
		);
		$fieldset->addField('title', 'text', [
				'label' => __('Title'),
				'name'  => 'title'
			]
		);
		$fieldset->addField('hold_day', 'text', [
				'name'  => 'hold_day',
				'label' => __('Holding Transaction For'),
				'note'  => 'day(s)'
			]
		);

		$form->addValues($this->prepareTransactionData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * @return mixed
	 */
	public function prepareTransactionData()
	{
		$transaction = $this->_coreRegistry->registry('current_transaction');
		if ($customerId = $this->_backendSession->getTransactionCustomerId()) {
			$account = $this->accountFactory->create();
			$account->load($customerId, 'customer_id');
			if ($account->getId()) {
				$customer = $this->customerFactory->create();
				$customer->load($customerId);
				$transaction->setCustomerId($customerId)
					->setCustomerName($customer->getName() . ' <' . $customer->getEmail() . '>');
			}
		}

		return $transaction->getData();
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return __('Transaction');
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
