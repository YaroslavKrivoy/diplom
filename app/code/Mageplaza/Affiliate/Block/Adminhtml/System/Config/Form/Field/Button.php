<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Mageplaza\Affiliate\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * "Reset to Defaults" button renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Button extends \Magento\Config\Block\System\Config\Form\Field
{

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		array $data = []
	) {
		parent::__construct($context, $data);
	}

	/**
	 * Set template
	 *
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('Mageplaza_Affiliate::system/config/restoretraffic.phtml');
	}

	/**
	 * Get robots.txt custom instruction default value
	 *
	 * @return string
	 */
	public function getRestoreUrl()
	{
		return $this->getUrl('mageplaza_affiliate/traffic/restore');
	}

	/**
	 * Generate button html
	 *
	 * @return string
	 */
	public function getButtonHtml()
	{
		$button = $this->getLayout()->createBlock(
			'Magento\Backend\Block\Widget\Button'
		)->setData(
			[
				'id' => 'restore_banner_traffic',
				'label' => __('Reset banner traffic'),
				'onclick' => 'javascript:conformation(); return false;',
			]
		);

		return $button->toHtml();
	}

	/**
	 * Render button
	 *
	 * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 */
	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		// Remove scope label
		$element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
		return parent::render($element);
	}

	/**
	 * Return element html
	 *
	 * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		return $this->_toHtml();
	}
}
