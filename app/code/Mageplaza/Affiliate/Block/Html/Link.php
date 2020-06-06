<?php

namespace Mageplaza\Affiliate\Block\Html;

use \Magento\Framework\View\Element\Template\Context;
use \Mageplaza\Affiliate\Helper\Data;

class Link extends \Magento\Framework\View\Element\Html\Link
{
	protected $helper;

	public function __construct(
		Context $context,
		Data $helper,
		array $data = []
	)
	{
		$this->helper = $helper;
		parent::__construct($context, $data);
	}

	protected function _toHtml()
	{
		$type = $this->getType();
		if (strpos($this->helper->getAffiliateConfig('general/show_link'), $type) !== false) {
			return parent::_toHtml();
		}

		return '';
	}
}