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
namespace Mageplaza\Affiliate\Block\Adminhtml\Transaction;

/**
 * Class View
 * @package Mageplaza\Affiliate\Block\Adminhtml\Transaction
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
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
	 * Initialize Transaction edit block
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_blockGroup = 'Mageplaza_Affiliate';
		$this->_controller = 'adminhtml_transaction';
		$this->_mode       = 'view';

		parent::_construct();

		$this->buttonList->remove('save');
		$this->buttonList->remove('delete');

		$transaction = $this->getTransaction();
		if ($transaction->getStatus() != \Mageplaza\Affiliate\Model\Transaction\Status::STATUS_CANCELED) {
			$confirm = __('Are you sure you want to cancel this transaction?');
			$this->buttonList->update('reset', 'label', __('Cancel'));
			$this->buttonList->update('reset', 'class', 'cancel');
			$this->buttonList->update(
				'reset',
				'onclick',
				'deleteConfirm(\'' . $confirm . '\', \'' . $this->getCancelUrl() . '\')'
			);
		}

		if ($transaction->getStatus() == \Mageplaza\Affiliate\Model\Transaction\Status::STATUS_HOLD) {
			$confirm = __('Are you sure you want to complete this transaction?');
			$this->addButton(
				'complete',
				[
					'label'   => __('Complete'),
					'onclick' => 'deleteConfirm(\'' . $confirm . '\', \'' . $this->getCompleteUrl() . '\')',
					'class'   => 'complete'
				],
				-1
			);
		}
	}

	/**
	 * Retrieve text for header element depending on loaded Transaction
	 *
	 * @return string
	 */
	public function getHeaderText()
	{
		return __("View Transaction '%1'", $this->escapeHtml($this->getTransaction()->getId()));
	}

	/**
	 * Get Cancel Transaction url
	 *
	 * @return string
	 */
	public function getCancelUrl()
	{
		return $this->getUrl('affiliate/transaction/cancel', ['id' => $this->getTransaction()->getId()]);
	}

	/**
	 * Get Complete Transaction url
	 *
	 * @return string
	 */
	public function getCompleteUrl()
	{
		return $this->getUrl('affiliate/transaction/complete', ['id' => $this->getTransaction()->getId()]);
	}

	/**
	 * @return mixed
	 */
	public function getTransaction()
	{
		return $this->_coreRegistry->registry('current_transaction');
	}
}
