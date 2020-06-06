<?php
namespace Mageplaza\Affiliate\Block\Account;

class Withdraw extends \Mageplaza\Affiliate\Block\Account
{
	private $_formData;

	public function getFormData($field = '')
	{
		if ($field) {
			if (isset($this->_formData[$field])) {
				return $this->_formData[$field];
			} else {
				return '';
			}
		}

		return $this->_formData;
	}

	public function setFormData($data)
	{
		$this->_formData = $data;

		return;
	}

	protected function _prepareLayout()
	{
		$this->pageConfig->getTitle()->set(__('My Withdrawal'));

		parent::_prepareLayout();

		if (!self::getFormData()) {
			$_formData = new \Magento\Framework\DataObject;

			$postedData = $this->customerSession->getWithdrawFormData(true);
			if ($postedData) {
				$_formData->addData($postedData);
			}

			self::setFormData($_formData);
		}
	}

	public function getWithdrawPostUrl()
	{
		return $this->getUrl('*/*/withdrawpost');
	}

	public function isAllowWithdraw()
	{
		return sizeof($this->getMethods()) && $this->_affiliateHelper->getAffiliateConfig('withdraw/allow_request');
	}

	public function convertPrice($value)
	{
		return $this->objectManager->create('Magento\Directory\Model\PriceCurrency')->convert($value);
	}

	/**
	 * Get tax fee withdraw
	 * @return string
	 */
	public function getFeeConfig()
	{
		$config = array();

		$paymentConfig = $this->getMethods();
		foreach ($paymentConfig as $code => $payment) {
			$fee = isset($payment['fee']) ? $payment['fee'] : 0;
			if (strpos($fee, '%') != false) {
				$type = 'percent';
				$fee  = floatval(trim($fee, '%'));
				if ($fee <= 0) {
					continue;
				}
			} else {
				$type = 'fix';
				if (floatval($fee) <= 0) {
					continue;
				}
				$fee = $this->convertPrice(floatval($fee));
			}

			$config[$code] = array(
				'type'  => $type,
				'value' => $fee
			);
		}

		return $this->jsonHelper->jsonEncode($config);
	}

	public function getMethodHtml($code)
	{
		$method = $this->paymentHelper->getMethodModel($code);

		return $method->getMethodHtml();
	}

	public function getWithdrawPolicy()
	{
		$policy = [];

		if ($min_balance = $this->_affiliateHelper->getAffiliateConfig('withdraw/minimum_balance')) {
			$policy[] = __('You can request withdraw when your balance equal or greater than %1', $this->formatPrice($min_balance));
		}

		if ($min = $this->_affiliateHelper->getAffiliateConfig('withdraw/minimum')) {
			$policy[] = __('You can withdraw a minimum %1', $this->formatPrice($min));
		}

		if ($max = $this->_affiliateHelper->getAffiliateConfig('withdraw/maximum')) {
			$policy[] = __('You can withdraw a maximum %1', $this->formatPrice($max));
		}

		return $policy;
	}

	public function getMethods()
	{
		return $this->paymentHelper->getActiveMethods();
	}
}
