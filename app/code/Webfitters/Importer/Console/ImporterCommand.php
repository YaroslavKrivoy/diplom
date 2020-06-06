<?php
namespace Webfitters\Importer\Console;

abstract class ImporterCommand extends \Symfony\Component\Console\Command\Command {
	
	const DSN = 'mysql:host=localhost;dbname=blackwing;';

	protected $db;
	protected $user;
	protected $password;

	public function __construct(
		\Magento\Framework\App\State $state,
		$name = null, 
		array $data = []
	){
		parent::__construct($name);
		$this->state = $state;
		$config = require(dirname(__FILE__).'/../../../../etc/env.php');
		$this->user = $config['db']['connection']['default']['username'];
		$this->password = $config['db']['connection']['default']['password'];
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		$this->db = new \PDO(static::DSN, $this->user, $this->password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
		$this->state->setAreaCode('adminhtml');
	}

}