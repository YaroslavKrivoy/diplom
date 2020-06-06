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
namespace Mageplaza\Affiliate\Block\Adminhtml\Withdraw\Create\Tab;

use Mageplaza\Affiliate\Helper\Payment;

/**
 * Class Withdraw
 * @package Mageplaza\Affiliate\Block\Adminhtml\Withdraw\Create\Tab
 */
class Withdraw extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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

	protected $_method;

	protected $_paymentHelper;

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
		\Mageplaza\Affiliate\Model\Withdraw\Method $method,
		Payment $payment,
		array $data = []
	)
	{
		$this->pricingHelper   = $pricingHelper;
		$this->customerFactory = $customerFactory;
		$this->accountFactory  = $accountFactory;
		$this->_method         = $method;
		$this->_paymentHelper = $payment;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		/** @var \Mageplaza\Affiliate\Model\Withdraw $withdraw */
		$form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('withdraw_');
		$form->setFieldNameSuffix('withdraw');

		$fieldset = $form->addFieldset('base_fieldset', [
				'legend' => __('Withdraw Information'),
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
				'note'     => __('Include fee.')
			]
		);
		$fieldset->addField('fee', 'text', [
				'label'    => __('Fee'),
				'name'     => 'fee',
				'class'    => 'validate-number',
				'note'     => __('If empty, configuration value will be used.')
			]
		);

		$paymentField = $fieldset->addField('payment_method', 'select', [
			'name'     => 'payment_method',
			'label'    => __('Payment Method'),
			'required' => true,
			'values'   => $this->_method->toOptionArray()
		]);

		$infoFieldset = $form->addFieldset('info_fieldset', [
				'legend' => __('Payment Detail'),
				'class'  => 'fieldset-wide'
			]
		);

		$dependence = $this->getLayout()->createBlock(
			'Magento\Backend\Block\Widget\Form\Element\Dependence'
		)->addFieldMap($paymentField->getHtmlId(), $paymentField->getName());

		foreach($this->_paymentHelper->getActiveMethods() as $method => $config){
			$fields = $this->_paymentHelper->getMethodModel($method)->getMethodDetail();
			foreach($fields as $key => $field){
				$detailField = $infoFieldset->addField($key, $field['type'], $field);
				$dependence->addFieldMap($detailField->getHtmlId(), $detailField->getName())
					->addFieldDependence($detailField->getName(), $paymentField->getName(), $method);
			}
		}

		$this->setChild('form_after', $dependence);

		$form->addValues($this->prepareWithdrawData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * @return mixed
	 */
	public function prepareWithdrawData()
	{
		$withdraw = $this->_coreRegistry->registry('current_withdraw');
		if ($customerId = $this->_backendSession->getWithdrawCustomerId()) {
			$account = $this->accountFactory->create();
			$account->load($customerId, 'customer_id');
			if ($account->getId()) {
				$customer = $this->customerFactory->create();
				$customer->load($customerId);
				$withdraw->setCustomerId($customerId)
					->setCustomerName($customer->getName() . ' <' . $customer->getEmail() . '>');
			}
		}

		return $withdraw->getData();
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return __('Withdraw');
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
