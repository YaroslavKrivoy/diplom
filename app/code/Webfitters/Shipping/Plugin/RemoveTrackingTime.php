<?php
namespace Webfitters\Shipping\Plugin;

class RemoveTrackingTime {

	public function afterFormatDeliveryDateTime(\Magento\Shipping\Block\Tracking\Popup $subject, $result){
		return \Carbon\Carbon::parse($result)->format('M d, Y');
	}

}