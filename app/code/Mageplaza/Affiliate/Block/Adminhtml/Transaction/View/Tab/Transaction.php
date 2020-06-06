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
namespace Mageplaza\Affiliate\Block\Adminhtml\Transaction\View\Tab;

use \Mageplaza\Affiliate\Model\Transaction\Type;
use \Mageplaza\Affiliate\Model\Transaction\Status;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Transaction
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction\View\Tab
 */
class Transaction extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * @var \Mageplaza\Affiliate\Model\Transaction\Type
	 */
	protected $_transactionType;

	/**
	 * @var \Mageplaza\Affiliate\Model\Transaction\Status
	 */
	protected $_transactionStatus;

	/**
	 * @type \Magento\Customer\Model\CustomerFactory
	 */
	protected $customerFactory;

	/**
	 * @type \Magento\Framework\Pricing\Helper\Data
	 */
	protected $pricingHelper;

	/**
	 * @type \Magento\Framework\Json\Helper\Data
	 */
	protected $jsonHelper;

	/**
	 * @type \Magento\User\Model\UserFactory
	 */
	protected $user;

	/**
	 * @param \Mageplaza\Affiliate\Model\Transaction\Type $type
	 * @param \Mageplaza\Affiliate\Model\Transaction\Status $status
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\User\Model\UserFactory $userFactory
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
	 * @param array $data
	 */
	public function __construct(
		Type $type,
		Status $status,
		JsonHelper $jsonHelper,
		\Magento\User\Model\UserFactory $userFactory,
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Framework\Pricing\Helper\Data $pricingHelper,
		array $data = []
	)
	{
		$this->jsonHelper         = $jsonHelper;
		$this->user               = $userFactory;
		$this->pricingHelper      = $pricingHelper;
		$this->customerFactory    = $customerFactory;
		$this->_transactionType   = $type;
		$this->_transactionStatus = $status;
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
		$transaction = $this->_coreRegistry->registry('current_transaction');
		$form        = $this->_formFactory->create();
		$form->setHtmlIdPrefix('transaction_');
		$form->setFieldNameSuffix('transaction');
		$fieldset = $form->addFieldset('base_fieldset', [
				'legend' => __('Transaction Information'),
				'class'  => 'fieldset-wide'
			]
		);

		$customerModel = $this->customerFactory->create()->load($transaction->getCustomerId());
		$fieldset->addField('affiliate_account', 'link', [
				'href'   => $this->getUrl('affiliate/account/edit', array('id' => $transaction->getAccountId())),
				'name'   => 'affiliate_account',
				'value'  => $customerModel->getName() . ' <' . $this->escapeHtml($customerModel->getEmail()) . '>',
				'label'  => __('Affiliate Account'),
				'target' => '_blank',
				'style'  => 'text-decoration: none',
				'class'  => 'control-value'
			]
		);

		$transactionType = $this->_transactionType->getOptionHash();
		$fieldset->addField('type', 'note', [
				'label' => __('Type'),
				'text'  => $transactionType[$transaction->getType()]
			]
		);
		$fieldset->addField('title', 'note', [
				'label' => __('Title'),
				'text'  => $transaction->getTitle()
			]
		);
		$fieldset->addField('amount', 'note', [
				'label' => __('Amount'),
				'text'  => $this->pricingHelper->currency($transaction->getAmount())
			]
		);
		$transactionStatus = $this->_transactionStatus->getOptionHash();
		$fieldset->addField('status', 'note', [
				'label' => __('Status'),
				'text'  => $transactionStatus[$transaction->getStatus()]
			]
		);

		if ($transaction->getOrderId()) {
			$fieldset->addField('order', 'link', [
					'href'   => $this->getUrl('sales/order/view', array('id' => $transaction->getOrderId())),
					'name'   => 'order',
					'value'  => '#' . $transaction->getOrderIncrementId(),
					'label'  => __('Order'),
					'target' => '_blank',
					'style'  => 'text-decoration: none',
					'class'  => 'control-value'
				]
			);
		}

		if ($transaction->getExtraContent() && ($transaction->getType() == Type::ADMIN)) {
			$extraContent = $this->jsonHelper->jsonDecode($transaction->getExtraContent());
			if (is_array($extraContent) && isset($extraContent['admin_id'])) {
				$admin = $this->user->create()->load($extraContent['admin_id']);
				$href  = $this->getUrl('adminhtml/user/edit', array('user_id' => $admin->getId()));
				$fieldset->addField('admin_account', 'link', [
						'label'  => __('Created by'),
						'href'   => $href,
						'value'  => $admin->getName() . ' <' . $this->escapeHtml($admin->getEmail()) . '>',
						'target' => '_blank',
						'style'  => 'text-decoration: none',
						'class'  => 'control-value'
					]
				);
			}
		}

		if ($transaction->getStatus() == Status::STATUS_HOLD && $transaction->getHoldingTo()) {
			$fieldset->addField('holding_to', 'note', array(
				'label' => __('This transaction will be holed to'),
				'text'  => $this->_localeDate->formatDate($transaction->getHoldingTo(), \IntlDateFormatter::MEDIUM, true)
			));
		}

		$fieldset->addField('created_at', 'note', [
				'label' => __('Created Time'),
				'text'  => $this->_localeDate->formatDate($transaction->getCreatedAt(), \IntlDateFormatter::MEDIUM, true)
			]
		);

		$form->addValues($transaction->getData());
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
