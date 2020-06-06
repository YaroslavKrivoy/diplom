<?php
namespace Mageplaza\Affiliate\Model\Payment\Methods;

class Offline extends \Mageplaza\Affiliate\Model\Payment\Methods
{
	public function getMethodDetail()
	{
		return [
			'offline_address' => [
				'type'     => 'textarea',
				'label'    => __('Address'),
				'name'     => 'offline_address'
			]
		];
	}

	public function getMethodHtml(){

	}
}