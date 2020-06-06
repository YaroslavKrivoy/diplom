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

class Campaign extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
	 * Country options
	 *
	 * @var \Magento\Config\Model\Config\Source\Yesno
	 */
	protected $_boolean;

	/**
	 * Status options
	 *
	 * @var \Mageplaza\Affiliate\Model\Campaign\Status
	 */
	protected $_status;

	/**
	 * @type \Magento\Store\Model\System\Store
	 */
	protected $_store;

	/**
	 * @type \Mageplaza\Affiliate\Model\Account\Group
	 */
	protected $_group;

	/**
	 * @type \Mageplaza\Affiliate\Model\Campaign\Display
	 */
	protected $_display;

	/**
	 * @param \Magento\Config\Model\Config\Source\Yesno $boolean
	 * @param \Mageplaza\Affiliate\Model\Campaign\Status $status
	 * @param \Mageplaza\Affiliate\Model\Account\Group $group
	 * @param \Mageplaza\Affiliate\Model\Campaign\Display $display
	 * @param \Magento\Store\Model\System\Store $store
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param array $data
	 */
	public function __construct(
		\Magento\Config\Model\Config\Source\Yesno $boolean,
		\Mageplaza\Affiliate\Model\Campaign\Status $status,
		\Mageplaza\Affiliate\Model\Account\Group $group,
		\Mageplaza\Affiliate\Model\Campaign\Display $display,
		\Magento\Store\Model\System\Store $store,
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	)
	{
		$this->_boolean = $boolean;
		$this->_status  = $status;
		$this->_store   = $store;
		$this->_group   = $group;
		$this->_display = $display;
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
		$fieldset = $form->addFieldset('base_fieldset', [
			'legend' => __('Campaign Information'),
			'class'  => 'fieldset-wide'
		]);

		$fieldset->addField('name', 'text', [
			'name'     => 'name',
			'label'    => __('Name'),
			'title'    => __('Name'),
			'required' => true,
		]);
		$fieldset->addField('description', 'textarea', [
			'name'  => 'description',
			'label' => __('Description'),
			'title' => __('Description'),
		]);
		$fieldset->addField('status', 'select', [
			'name'     => 'status',
			'label'    => __('Status'),
			'title'    => __('Status'),
			'required' => true,
			'values'   => $this->_status->toOptionArray()
		]);
		$fieldset->addField('website_ids', 'multiselect', [
			'name'     => 'website_ids',
			'label'    => __('Website IDs'),
			'title'    => __('Website IDs'),
			'required' => true,
			'values'   => $this->_store->getWebsiteValuesForForm(),
		]);
		$fieldset->addField('affiliate_group_ids', 'multiselect', [
			'name'     => 'affiliate_group_ids',
			'label'    => __('Affiliate Groups'),
			'title'    => __('Affiliate Groups'),
			'required' => true,
			'values'   => $this->_group->toOptionArray(),
		]);
		$fieldset->addField('display', 'select', [
			'name'     => 'display',
			'label'    => __('Display'),
			'title'    => __('Display'),
			'required' => true,
			'values'   => $this->_display->toOptionArray()
		]);
		$fieldset->addField('from_date', 'date', [
			'name'        => 'from_date',
			'label'       => __('Active From Date'),
			'title'       => __('Active From Date'),
			'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
			'class'       => 'validate-date',
		]);
		$fieldset->addField('to_date', 'date', [
			'name'        => 'to_date',
			'label'       => __('Active To Date'),
			'title'       => __('Active To Date'),
			'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
			'class'       => 'validate-date',
		]);
		$fieldset->addField('sort_order', 'text', [
			'name'  => 'sort_order',
			'label' => __('Sort Order'),
			'title' => __('Sort Order'),
		]);

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
		return __('Campaign Information');
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
