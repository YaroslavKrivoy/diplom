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
namespace Mageplaza\Affiliate\Block\Adminhtml\Withdraw;

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
	 * Initialize Withdraw edit block
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_objectId   = 'withdraw_id';
		$this->_blockGroup = 'Mageplaza_Affiliate';
		$this->_controller = 'adminhtml_withdraw';
		parent::_construct();
		$this->buttonList->update('save', 'label', __('Save'));
		$this->buttonList->add(
			'save-and-approve',
			[
				'label'          => __('Save and Approve'),
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
		$this->buttonList->remove('delete');
	}

	/**
	 * Retrieve text for header element depending on loaded Withdraw
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		/** @var \Mageplaza\Affiliate\Model\Withdraw $withdraw */
		$withdraw = $this->_coreRegistry->registry('affiliate_withdraw_data');
		if ($withdraw->getId()) {
			return __("Edit Withdraw '%1'", $this->escapeHtml($withdraw->getId()));
		}

		return __('New Withdraw');
	}
}
