<?php
namespace Webfitters\SocialMedia\Cron;

class FacebookImport {

	public function execute(){
		/*
		blackwing247/feed
		*/


		$test = fopen(dirname(__FILE__).'/testing.txt', 'w+');
		ob_start();
		var_dump('coolioasdf...');
		fwrite($test, ob_get_clean());
		fclose($test);
		return $this;
	}

}