<?php

namespace Mageplaza\Affiliate\Helper;

class Payment extends Data
{
	private $_methods = null;
	private $_activeMethods = null;

	const CONFIG_PAYMENT_METHODS = 'payment_method';
	const SYSTEM_PAYMENT_METHODS = 'withdraw';
	const PAYMENT_METHOD_SELECT_NAME = 'payment_method';

	public function getMethodModel($code)
	{
		$method = $this->getAllMethods();

		$methodModel = $this->objectManager->create($method[$code]['model']);

		return $methodModel;
	}

	public function getActiveMethods()
	{
		if (is_null($this->_activeMethods)) {
			$methods = $this->getAllMethods();
			foreach ($methods as $code => $config) {
				if (!isset($config['active']) || !$config['active']) {
					unset($methods[$code]);
				}
			}
			$this->_activeMethods = $methods;
		}

		return $this->_activeMethods;
	}

	public function getAllMethods()
	{
		if (is_null($this->_methods)) {
			$methodConfig  = $this->getAffiliateConfig(self::SYSTEM_PAYMENT_METHODS . '/payment_method');
			$methodConfig  = @unserialize($methodConfig);
			$initialMethod = $this->getAffiliateConfig('payment_method');

			foreach ($initialMethod as $code => $method) {
				if (isset($methodConfig[$code])) {
					$initialMethod[$code] = array_merge($method, $methodConfig[$code]);
				}
			}

			$this->_methods = $initialMethod;
		}

		return $this->_methods;
	}

	public function getFee($code, $amount)
	{
		$methodConfig = $this->getAllMethods();

		if (!empty($methodConfig) && isset($methodConfig[$code]) && isset($methodConfig[$code]['fee'])) {
			$feeConfig = $methodConfig[$code]['fee'];
			if (strpos($feeConfig, '%')) {
				$fee = floatval(trim($feeConfig, '%'));

				return $amount . ($fee / (100 + $fee));
			} else {
				return floatval($feeConfig);
			}
		}

		return 0;
	}
}