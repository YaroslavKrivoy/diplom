<?php
namespace Webfitters\Importer\Console\Commands;

class SplitOrders extends \Webfitters\Importer\Console\ImporterCommand {

	protected $dir;

	public function __construct(
		\Magento\Framework\App\State $state,
		\Magento\Framework\Filesystem\DirectoryList $dir,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->dir = $dir;
	}

	protected function configure(){
		$this
			->setName('webfitters:splitorders')
			->setDescription('Imports orders from live database by chunks to prevent memory leaks.')
			->addOption('chunk', 'c', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'What should the chunk size be?', 50)
			->addOption('processes', 'p', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'How many simultaneous processes?', 5);
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$query = $this->db->query('SELECT COUNT(*) AS `total` FROM `gs_order` LIMIT 0, 1;')->fetchAll(\PDO::FETCH_OBJ);
		$total = $query[0]->total;
		$processes = intval($input->getOption('processes'));
		$chunk = intval($input->getOption('chunk'));
		$pages = ceil($total/$chunk);
		$php = trim(shell_exec('which php'));
		$pids = [];
		for($i = 0; $i < $pages; $i++){
			echo $i;
			while(count($pids) == $processes){
				foreach($pids as $key => $pid){
					if(!$this->isRunning($pid)){
						unset($pids[$key]);
					}
				}
				sleep(1);	
			}
			$pid = shell_exec(sprintf('%s > /dev/null 2>&1 & echo $!', $php.' '.$this->dir->getRoot().'/bin/magento webfitters:order --chunk='.$chunk.' --page='.$i));
			$pids[] = $pid;
			echo 'Started process '.$pid.' to do page '.$i.PHP_EOL;
		}
	}

	private function isRunning($pid){
		try {
			$result = shell_exec(sprintf('ps %d', $pid));
            if(count(preg_split("/\n/", $result)) > 2) {
                return true;
            }
		} catch(\Exception $e) {}
		return false;
	}

}