<?php
namespace Webfitters\Development\Console\Commands;

class DevelopmentReset extends \Symfony\Component\Console\Command\Command {

	protected $directory;

	public function __construct(
		\Magento\Framework\Filesystem\DirectoryList $directory,
		$name = null, 
		array $data = []
	){
		$this->directory = $directory;
		parent::__construct($name);
	}

	protected function configure(){
		$this->setName('development:reset')->setDescription('Utility for development.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		$files = glob($this->directory->getRoot().'/pub/static/*/*/*/en_US/Webfitters_*', GLOB_NOSORT);
		foreach($files as $module){
			$this->unlink($module);
		}
		$files = glob($this->directory->getRoot().'/pub/static/*/*/*/en_US/Smile_*', GLOB_NOSORT);
		foreach($files as $module){
			$this->unlink($module);
		}
		$classes = glob($this->directory->getRoot().'/generated/code/Webfitters/*', GLOB_NOSORT);
		foreach($classes as $class){
			$this->unlink($class);
		}
	}

	protected function unlink($path){
		$files = array_diff(scandir($path), array('.', '..'));
		foreach($files as $file){
			if(is_dir($path.'/'.$file)){
				$this->unlink($path.'/'.$file);
			}
			if(strpos($file, '.css')!==FALSE || strpos($file, '.js')!==FALSE || strpos($file, '.php') !== FALSE){
				unlink($path.'/'.$file);
			}
		}
	}

}