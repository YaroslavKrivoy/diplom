<?php
namespace Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget;
use Magento\Framework\Registry;

/**
 * Class Arraycommission
 * @package Mageplaza\Affiliate\Block\Adminhtml\Campaign\Edit\Tab\Commissions
 */
class Arraycommission extends Widget implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
	const TYPE_SALE_PERCENT = 1;
	const TYPE_PROFIT_PERCENT = 2;
	const TYPE_FIXED = 3;

	protected $_template = 'commissions/list/tier.phtml';
	protected $_element;
	protected $_registry;
	protected $affiliateHelper;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Mageplaza\Affiliate\Helper\Data $helper,
		Registry $registry,
		array $data = []
	)
	{
		$this->affiliateHelper = $helper;
		$this->_registry = $registry;
		parent::__construct($context, $data);
	}

	/**
	 * @param AbstractElement $element
	 * @return string
	 */

	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		$this->setElement($element);

		return $this->toHtml();
	}

	public function getCommissionType()
	{
		return array(
			array('value' => self::TYPE_SALE_PERCENT, 'label' => __('Percentage of grand total')),
//			array('value' => self::TYPE_PROFIT_PERCENT, 'label' => __('Percentage of total profits')),
			array('value' => self::TYPE_FIXED, 'label' => __('Fixed amount')),
		);
	}

	public function getCommissionData()
	{
		$campaign  = $this->_registry->registry('current_campaign');
		$comission = $campaign->getCommission();
		if (!is_array($comission)) {
			$comission = @unserialize($comission);
		}

		return $comission;
	}

	public function setElement(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		$this->_element = $element;

		return $this;
	}

	public function getElement()
	{
		return $this->_element;
	}

	public function isMultiWebsites()
	{
		return !$this->_storeManager->isSingleStoreMode();
	}

	public function getAddButtonHtml()
	{
		return $this->getChildHtml('add_button');
	}

	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
}
