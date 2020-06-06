<?php
namespace Webfitters\Importer\Console\Commands;

class CustomerGroupCreate extends \Webfitters\Importer\Console\ImporterCommand {

	protected $group;
	
	public function __construct(
		\Magento\Framework\App\State $state,
		\Magento\Customer\Model\GroupFactory $group,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->group = $group;
	}

	protected function configure(){
		$this->setName('webfitters:customergroups')->setDescription('Imports customer groups from live database.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$groups = $this->db->query('SELECT * FROM `gs_customer_category`;')->fetchAll(\PDO::FETCH_OBJ);
		foreach($groups as $group){
			$g = $this->group->create()->load($group->name, 'customer_group_code');
			if(!$g->getCustomerGroupId()){
				$g = $this->group->create()->setCode($group->name)->setTaxClassId(3)->save();
			}
		}
	}

}