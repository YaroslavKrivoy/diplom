<?php
namespace Webfitters\NiceCaptcha\Plugin;

class CaptchaModify {
	
	public function __construct(){
		die('cool');
	}

	public function afterGetDotNoiseLevel(\Magento\Captcha\Model\DefaultModel $captcha, $result) {
		dump($result);
		die();
    }
	
}