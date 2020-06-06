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
namespace Mageplaza\Affiliate\Block\Adminhtml\Campaign;

/**
 * Class Edit
 * @package Mageplaza\Affiliate\Block\Adminhtml\Campaign
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * constructor
	 *
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Backend\Block\Widget\Context $context,
		array $data = []
	)
	{
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $data);
	}

	/**
	 * Initialize Campaign edit block
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_blockGroup = 'Mageplaza_Affiliate';
		$this->_controller = 'adminhtml_campaign';
		parent::_construct();
		$this->buttonList->update('save', 'label', __('Save Campaign'));
		$this->buttonList->add(
			'save-and-continue',
			[
				'label'          => __('Save and Continue Edit'),
				'class'          => 'save',
				'data_attribute' => [
					'mage-init' => [
						'button' => [
							'event'  => 'saveAndContinueEdit',
							'target' => '#edit_form'
						]
					]
				]
			],
			-100
		);
	}

	/**
	 * Retrieve text for header element depending on loaded Campaign
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		/** @var \Mageplaza\Affiliate\Model\Campaign $campaign */
		$campaign = $this->_coreRegistry->registry('current_campaign');
		if ($campaign->getId()) {
			return __('Edit Campaign "%1"', $this->escapeHtml($campaign->getName()));
		}

		return __('New Campaign');
	}
}
