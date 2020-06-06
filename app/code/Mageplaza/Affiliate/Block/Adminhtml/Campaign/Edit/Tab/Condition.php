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

class Condition extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
	 */
	protected $_rendererFieldset;

	/**
	 * @var \Magento\Rule\Block\Conditions
	 */
	protected $_conditions;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
	 * @param \Magento\Rule\Block\Conditions $conditions
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
		\Magento\Rule\Block\Conditions $conditions,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	)
	{
		$this->_rendererFieldset = $rendererFieldset;
		$this->_conditions       = $conditions;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		/** @var \Mageplaza\Affiliate\Model\Campaign $campaign */
		$form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('rule_');

		$model    = $this->_coreRegistry->registry('current_campaign_rule');
		$renderer = $this->_rendererFieldset->setTemplate(
			'Magento_CatalogRule::promo/fieldset.phtml'
		)->setNewChildUrl(
			$this->getUrl('sales_rule/promo_quote/newConditionHtml/form/rule_conditions_fieldset')
		);
		$fieldset = $form->addFieldset('conditions_fieldset', [
			'legend' => __('Apply the rule only if the following conditions are met (leave blank for all products)'),
			'class'  => 'fieldset-wide'
		])->setRenderer($renderer);

		$fieldset->addField('conditions', 'text', [
			'name'  => 'conditions',
			'label' => __('Condition'),
			'title' => __('Condition')
		])
			->setRule($model)
			->setRenderer($this->_conditions);

		$form->setValues($model->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}


	/**
	 * {@inheritdoc}
	 */
	public function getTabLabel()
	{
		return __('Conditions');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTabTitle()
	{
		return __('Conditions');
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
}
