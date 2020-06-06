<?php
namespace Mageplaza\Affiliate\Model\Payment\Methods;

class Paypal extends \Mageplaza\Affiliate\Model\Payment\Methods
{
	public function getMethodDetail()
	{
		return [
			'paypal_email'          => [
				'type'     => 'text',
				'label'    => __('Paypal Email'),
				'name'     => 'paypal_email',
				'required' => true,
				'class'    => 'required-entry validate-email'
			],
			'paypal_transaction_id' => [
				'type'  => 'text',
				'label' => __('Transaction Id'),
				'name'  => 'paypal_transaction_id'
			]
		];
	}
}