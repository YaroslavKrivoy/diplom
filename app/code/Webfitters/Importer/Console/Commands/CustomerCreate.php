<?php
namespace Webfitters\Importer\Console\Commands;

class CustomerCreate extends \Webfitters\Importer\Console\ImporterCommand {

	protected $group;
	protected $customer;
	protected $address;
	protected $region;

	public function __construct(
		\Magento\Framework\App\State $state,
		\Magento\Customer\Model\ResourceModel\Group\Collection $group,
		\Magento\Customer\Model\CustomerFactory $customer,
		\Magento\Customer\Model\AddressFactory $address,
		\Magento\Directory\Model\RegionFactory $region,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->group = $group;
		$this->customer = $customer;
		$this->address = $address;
		$this->region = $region;
	}

	protected function configure(){
		$this->setName('webfitters:customers')->setDescription('Imports customers from live database.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$groups = $this->group->load();
		$mapped = array();
		foreach($groups as $group){
			$mapped[$group->getCustomerGroupCode()] = $group->getCustomerGroupId();
		}
		$customers = $this->db->query('
			SELECT `c`.*, `cc`.`name` AS `group` FROM `gs_customer` AS `c` 
			LEFT JOIN `gs_customer_category` AS `cc` ON `c`.`category_id` = `cc`.`id`;
		')->fetchAll(\PDO::FETCH_OBJ);
		$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($customers));
		foreach($customers as $c){
			if($c->email != '' && filter_var($c->email, FILTER_VALIDATE_EMAIL)){
				try {
					$customer = $this->customer->create()->setWebsiteId(1)->loadByEmail($c->email);
					if(!$customer->getEntityId()){
						$customer = $this->customer->create()->setWebsiteId(1);
						$customer->setPassword(bin2hex(openssl_random_pseudo_bytes(5)));
						$customer->setEmail($c->email);
					} 
					$customer->setFirstname(($c->firstname != '')?$c->firstname:'n/a');
					$customer->setLastname(($c->lastname != '')?$c->lastname:'n/a');
					$customer->setCreatedAt(date('Y-m-d H:i:s', strtotime($c->created)));
					$customer->setGroupId($mapped[$c->group]);
					$customer->save();
					$cd = $customer->getDataModel();
					$cd->setCustomAttribute('company', $c->company);
					$customer->updateData($cd);
					$customer->save();
					$addresses = $this->db->query('SELECT * FROM `gs_customer_address` WHERE `customer_id` = '.$c->id.';')->fetchAll(\PDO::FETCH_OBJ);
					$current = $customer->getAddresses();
					$all = array();

					foreach($current as $addr){
						$street = $addr->getStreet();
						if(count($street) > 0){
							$street = $street[0];
						}
						$all[$street.'|'.$addr->getPostcode()] = $addr;
					}
					foreach($addresses as $address){
						if(!isset($all[$address->address1.'|'.$address->zip])){
							$final = $this->address->create();
							if(count($all)==0){
								$final->setIsDefaultBilling(1);
								$final->setIsDefaultShipping(1);
							}
						} else {
							$final = $this->address->create()->load($all[$address->address1.'|'.$address->zip]->getId());
						}
						$final->setCustomerId($customer->getId());
						$final->setFirstname((($address->ca_firstname=='')?$customer->getFirstname():$address->ca_firstname));
						$final->setLastname((($address->ca_lastname=='')?$customer->getLastname():$address->ca_lastname));
						$final->setCountryId((($address->country=='USA' || $address->country=='')?'US':$address->country));
						$final->setPostcode(($address->zip != '')?$address->zip:'00000');
						$final->setCity(($address->city != '')?$address->city:'n/a');
						$final->setTelephone(($address->phone != '')?$address->phone:'n/a');
						$final->setFax($address->fax);
						$final->setCompany($c->company);
						$final->setRegion($this->region->create()->loadByCode($address->state, (($address->country=='USA'||$address->country=='')?'US':$address->country))->getId());
						$final->setStreet((trim($address->address1) != '' || trim($address->address2) != '')?$address->address1.' '.$address->address2:'n/a');
						$final->setSaveInAddressBook(true);
						$final->save();
						$all[$address->address1.'|'.$address->zip] = $final;
					}
				} catch(\Magento\Framework\Validator\Exception $e) {var_dump($e->getMessage()); echo PHP_EOL.PHP_EOL; var_dump($c); echo PHP_EOL.PHP_EOL;} catch (\Exception $e) {}
			}
			$progress->advance();
		}
		$progress->finish();
	}

}