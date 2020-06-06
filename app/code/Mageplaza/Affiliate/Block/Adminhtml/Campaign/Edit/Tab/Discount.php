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
namespace Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab;

class Discount extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	protected $_rendererFieldset;

	/**
	 * @var \Magento\Rule\Block\Actions
	 */
	protected $_ruleActions;

	/**
	 * @var \Magento\Config\Model\Config\Source\Yesno
	 */
	protected $_boolean;

	protected $_discount;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Config\Model\Config\Source\Yesno $boolean,
		\Magento\Rule\Block\Actions $ruleActions,
		\Mageplaza\Affiliate\Model\Campaign\Discount $discount,
		\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
		array $data = []
	)
	{
		$this->_rendererFieldset = $rendererFieldset;
		$this->_ruleActions      = $ruleActions;
		$this->_boolean          = $boolean;
		$this->_discount         = $discount;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTabLabel()
	{
		return __('Discounts');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTabTitle()
	{
		return __('Discounts');
	}

	/**
	 * {@inheritdoc}
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHidden()
	{
		return false;
	}

	/**
	 * Prepare form before rendering HTML
	 *
	 * @return $this
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function _prepareForm()
	{
		$model = $this->_coreRegistry->registry('current_campaign_rule');

		$form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('rule_');

		$fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Discount')]);

		$fieldset->addField('discount_action', 'select', [
			'name'   => 'discount_action',
			'label'  => __('Apply'),
			'title'  => __('Apply'),
			'values' => $this->_discount->toOptionArray()
		]);
		$fieldset->addField('discount_amount', 'text', [
			'name'  => 'discount_amount',
			'label' => __('Discount Amount'),
			'title' => __('Discount Amount'),
			'value' => '0'
		]);
//		$fieldset->addField('discount_qty', 'text', [
//			'name'  => 'discount_qty',
//			'label' => __('Maximum Qty Discount is Applied To'),
//			'title' => __('Maximum Qty Discount is Applied To'),
//			'value' => '0'
//		]);
//		$fieldset->addField('discount_step', 'text', [
//			'name'  => 'discount_step',
//			'label' => __('Discount Qty Step (Buy X)'),
//			'title' => __('Discount Qty Step (Buy X)'),
//			'value' => '0'
//		]);
		$fieldset->addField('apply_to_shipping', 'select', [
			'name'   => 'apply_to_shipping',
			'label'  => __('Apply to Shipping Amount'),
			'title'  => __('Apply to Shipping Amount'),
			'values' => $this->_boolean->toOptionArray(),
		]);
//		$fieldset->addField('free_shipping', 'select', [
//			'name'   => 'free_shipping',
//			'label'  => __('Free Shipping'),
//			'title'  => __('Free Shipping'),
//			'values' => $this->_boolean->toOptionArray(),
//		]);

		$fieldset->addField('discount_description', 'textarea', [
			'name'  => 'discount_description',
			'label' => __('Discount Description'),
			'title' => __('Discount Description'),
		]);

//		$renderer = $this->_rendererFieldset->setTemplate(
//			'Magento_CatalogRule::promo/fieldset.phtml'
//		)->setNewChildUrl(
//			$this->getUrl('sales_rule/promo_quote/newActionHtml/form/rule_actions_fieldset')
//		);
//
//		$fieldset = $form->addFieldset('actions_fieldset', [
//			'legend' => __(
//				'Apply the rule only to cart items matching the following conditions ' .
//				'(leave blank for all items).'
//			)
//		])->setRenderer($renderer);
//
//		$fieldset->addField('actions', 'text', [
//			'name'     => 'actions',
//			'label'    => __('Apply To'),
//			'title'    => __('Apply To'),
//			'required' => true
//		])
//			->setRule($model)
//			->setRenderer($this->_ruleActions);

		$form->setValues($model->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}
}