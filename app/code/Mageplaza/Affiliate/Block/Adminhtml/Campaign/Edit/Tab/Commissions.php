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

use \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission;

class Commissions extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
	 */
	protected $_rendererFieldset;

	/**
	 * @type \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission
	 */
	protected $arrayCommission;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions\Arraycommission $arraycommission
	 * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		Arraycommission $arraycommission,
		\Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	)
	{
		$this->_rendererFieldset = $rendererFieldset;
		$this->arrayCommission   = $arraycommission;
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
		$campaign = $this->_coreRegistry->registry('current_campaign');
		$form     = $this->_formFactory->create();
		$form->setHtmlIdPrefix('campaign_');
		$form->setFieldNameSuffix('campaign');


		$renderer = $this->_rendererFieldset->setTemplate(
			'Mageplaza_Affiliate::commissions/list.phtml'
		);


		$fieldset = $form->addFieldset('base_fieldset', [
			'legend' => __('Pay Per Sale'),
			'class'  => 'fieldset-wide'
		])->setRenderer($renderer);

		$fieldset->addField('commissions', 'text', [
			'name'  => 'commissions',
			'label' => __('Add Commission Type and Value'),
			'title' => __('Add Commission Type and Value')
		])->setRenderer($this->arrayCommission);

		$form->addValues($campaign->getData());
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
		return __('Commissions');
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
