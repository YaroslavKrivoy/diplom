<?php
namespace Mageplaza\Affiliate\Model\Payment;

use Magento\Framework\Json\Helper\Data as JsonHelper;

class Methods extends \Magento\Framework\DataObject
{
	protected $_jsonHelper;

	public function __construct(JsonHelper $helper)
	{
		$this->_jsonHelper = $helper;
	}

	public function getWithdrawInfoDetail()
	{
		$detail   = [];
		$withdraw = $this->getData('withdraw');
		foreach ($this->getMethodDetail() as $key => $value) {
			$detail[$key] = $withdraw->getData($key);
		}

		return $this->_jsonHelper->jsonEncode($detail);
	}

	public function getPaymentDetail()
	{
		$detail   = [];
		$withdraw = $this->getData('withdraw');
		if(!$withdraw->getPaymentDetails())
			return $detail;

		$paymentDetail = $this->_jsonHelper->jsonDecode($withdraw->getPaymentDetails());
		foreach ($this->getMethodDetail() as $key => $value) {
			if(isset($paymentDetail[$key])) {
				$detail[$key] = [
					'label' => $value['label'],
					'value' => $paymentDetail[$key]
				];
			}
		}

		return $detail;
	}
}