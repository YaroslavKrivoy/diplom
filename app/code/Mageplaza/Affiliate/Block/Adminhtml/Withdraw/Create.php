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

/**
 * Class Create
 * @package Mageplaza\Affiliate\Block\Adminhtml\Withdraw
 */
class Create extends \Magento\Backend\Block\Widget\Form\Container
{
	/**
	 * Constructor
	 *
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		array $data = []
	)
	{
		parent::__construct($context, $data);
	}

	/**
	 * Initialize Withdraw create block
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_blockGroup = 'Mageplaza_Affiliate';
		$this->_controller = 'adminhtml_withdraw';
		parent::_construct();

		$customerId = $this->_backendSession->getWithdrawCustomerId();

		$this->buttonList->add(
			'save-and-approve',
			[
				'label'          => __('Save and Approve'),
				'class'          => 'save',
				'id'             => 'save-and-approve_withdraw_top_button',
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

		$this->buttonList->update('save', 'label', __('Save'));
		$this->buttonList->update('back', 'id', 'back_withdraw_top_button');
		$this->buttonList->update('save', 'id', 'save_withdraw_top_button');

		$this->buttonList->update('reset', 'id', 'reset_withdraw_top_button');
		$this->buttonList->update('reset', 'label', __('Cancel'));
		$this->buttonList->update('reset', 'class', 'cancel');
		$this->buttonList->update('reset', 'onclick', 'setLocation(\'' . $this->getCancelUrl() . '\')');

		if ($customerId === null) {
			$this->buttonList->update('reset', 'style', 'display:none');
			$this->buttonList->update('save', 'style', 'display:none');
			$this->buttonList->update('save-and-approve', 'style', 'display:none');
		} else {
			$this->buttonList->update('back', 'style', 'display:none');
		}

		$this->buttonList->remove('delete');
	}

	/**
	 * Retrieve text for header element depending on loaded Withdraw
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		return __('New Withdraw');
	}

	/**
	 * Get cancel url
	 *
	 * @return string
	 */
	public function getCancelUrl()
	{
		return $this->getUrl('affiliate/withdraw_create/cancel');
	}
}
