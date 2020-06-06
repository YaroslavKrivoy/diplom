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
namespace Mageplaza\Affiliate\Model\Withdraw;

use Mageplaza\Affiliate\Helper\Payment;

class Method implements \Magento\Framework\Option\ArrayInterface
{
	protected $_paymentHelper;

	public function __construct(Payment $helper)
	{
		$this->_paymentHelper = $helper;
	}

	/**
	 * to option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		$options = [];

		foreach ($this->getOptionHash() as $value => $label) {
			$options[] = [
				'value' => $value,
				'label' => $label
			];
		}

		return $options;
	}

	public function getOptionHash()
	{
		$options        = [];
		$paymentMethods = $this->_paymentHelper->getActiveMethods();
		foreach ($paymentMethods as $key => $method) {
			$options[$key] = $method['label'];
		}

		return $options;
	}
}
