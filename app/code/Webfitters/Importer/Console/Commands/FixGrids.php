<?php
namespace Webfitters\Importer\Console\Commands;

class FixGrids extends \Webfitters\Importer\Console\ImporterCommand {

	public function __construct(
		\Magento\Framework\App\State $state,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
	}

	protected function configure(){
		$this
			->setName('webfitters:fix-orders')
			->setDescription('Fixes order grids from live database.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$orders = $this->db->query('SELECT `entity_id`, `customer_id` FROM `sales_order` LIMIT 79570, 100000;')->fetchAll(\PDO::FETCH_OBJ);
		$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($orders));
		foreach($orders as $order){
			if($order->customer_id){
				$this->db->query('UPDATE `sales_invoice` SET `customer_id` = '.$order->customer_id.' WHERE `order_id` = '.$order->entity_id.';')->execute();
				$this->db->query('UPDATE `sales_invoice_grid` SET `customer_id` = '.$order->customer_id.' WHERE `order_id` = '.$order->entity_id.';')->execute();
				$this->db->query('UPDATE `sales_shipment_grid` SET `customer_id` = '.$order->customer_id.' WHERE `order_id` = '.$order->entity_id.';')->execute();
			}
			$progress->advance();
		}
		$progress->finish();
	}

}