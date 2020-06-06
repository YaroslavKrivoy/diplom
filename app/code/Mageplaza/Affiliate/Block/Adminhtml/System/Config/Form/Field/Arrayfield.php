<?php
/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\Affiliate\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Mageplaza\Affiliate\Helper\Payment as HelperData;

/**
 * Backend system config datetime field renderer
 */
class Arrayfield extends FormField
{
	protected $element;
	protected $helper;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Mageplaza\Affiliate\Helper\Payment $dataHelper
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		HelperData $dataHelper,
		array $data = []
	)
	{
		$this->helper = $dataHelper;
		parent::__construct($context, $data);
	}

	protected function _construct()
	{
		$this->setTemplate('Mageplaza_Affiliate::system/config/array.phtml');
		parent::_construct();
	}

	/**
	 * Render text
	 *
	 * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 */
	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
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
		$this->element = $element;

		return $this->_toHtml();
	}

	public function getHtmlid()
	{
		return $this->element->getHtmlId();
	}

	public function getName()
	{

		return $this->element->getName();
	}

	public function getArrayRows()
	{
		$arrayRows = [];
		foreach ($this->helper->getAllMethods() as $key => $config) {
			$arrayRows[$key] = __($config['label']);
		}

		return $arrayRows;
	}

	public function getConfigData()
	{
		return $this->helper->getAffiliateConfig('withdraw/payment_method');
	}
}
