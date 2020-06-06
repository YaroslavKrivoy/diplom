<?php
namespace Webfitters\Importer\Console\Commands;

class OrderCreate extends \Webfitters\Importer\Console\ImporterCommand {

	protected $product;
	protected $customer;
	protected $cartManagement;
	protected $cartRepo;
	protected $quote;
	protected $store;
	protected $customerRepo;
	protected $order;
	protected $transaction;
	protected $invoice;
	protected $orderRepo;
	protected $credit;
	protected $refund;
	protected $tracking;

	public function __construct(
		\Magento\Framework\App\State $state,
		\Magento\Catalog\Model\ProductFactory $product, 
		\Magento\Customer\Model\CustomerFactory $customer,
		\Magento\Quote\Api\CartRepositoryInterface $cartRepo,
		\Magento\Quote\Model\QuoteFactory $quote, 
		\Magento\Framework\DB\Transaction $transaction,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
		\Magento\Sales\Model\Order\CreditmemoFactory $credit,
		\Magento\Sales\Model\Order\Shipment\TrackFactory $tracking,
		\Magento\Catalog\Helper\Product $phelper,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->phelper = $phelper;
		$this->product = $product;
		$this->customer = $customer;
		$this->cartRepo = $cartRepo;
		$this->quote = $quote;
		$this->store = $store;
		$this->transaction = $transaction;
		$this->customerRepo = $customerRepo;
		$this->credit = $credit;
		$this->tracking = $tracking;
	}

	protected function configure(){
		$this
			->setName('webfitters:orders')
			->setDescription('Imports orders from live database.')
			->addOption('chunk', 'c', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'What should the chunk size be?', 50)
			->addOption('page', 'p', \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'What page are we on?', 0);
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$this->cartManagement = $om->get('Magento\Quote\Api\CartManagementInterface');
		$this->order = $om->get('Magento\Sales\Model\OrderFactory');
		$this->orderRepo = $om->get('Magento\Sales\Model\OrderFactory');
		$this->invoice = $om->get('Magento\Sales\Model\Service\InvoiceService');
		$this->refund = $om->get('Magento\Sales\Model\Service\CreditmemoService');
		$this->phelper->setSkipSaleableCheck(true);
		$helper = new \Webfitters\Importer\Model\Order(
			$this->product,
			$this->cartManagement,
			$this->orderRepo, 
			$this->invoice,
			$this->transaction,
			$this->credit,
			$this->refund,
			$om->get('Magento\Sales\Model\Convert\Order'),
			$this->cartRepo,
			$this->store,
			$this->tracking
		);
		$orders = $this->db->query('SELECT 
			`o`.`id`,
			`o`.`customer_id`, 
			`o`.`date_ordered`, 
			`o`.`shipping_date`, 
			`o`.`shipping_price`, 
			`o`.`shipping_cost`, 
			`o`.`shipper`, 
			`o`.`shipper_service_code`,
			`o`.`shipper_service`,
			`o`.`shipper_tracking`, 
			`o`.`wh_req_delivery_date`,
			`o`.`shipping_firstname`, 
			`o`.`shipping_lastname`, 
			`o`.`shipping_address1`, 
			`o`.`shipping_address2`, 
			`o`.`shipping_city`, 
			`o`.`shipping_state`, 
			`o`.`shipping_zip`, 
			`o`.`shipping_phone`,
			`o`.`shipping_instructions`,
			`o`.`status`,
			`c`.`email` as `customer_email`,
			`c`.`firstname` as `customer_firstname`,
			`c`.`lastname` as `customer_lastname`,
			`a`.`address1` as `customer_address`,
			`a`.`city` as `customer_city`,
			`a`.`state` as `customer_state`,
			`a`.`zip` as `customer_zip`,
			`a`.`phone` as `customer_phone`
			FROM `gs_order` AS `o` 
			LEFT JOIN `gs_customer` AS `c` ON `o`.`customer_id` = `c`.`id`
			LEFT JOIN `gs_customer_address` AS `a` ON `a`.`customer_id` = `c`.`id` AND `a`.`type` = "billing" 
			LIMIT '.(intval($input->getOption('page'))*intval($input->getOption('chunk'))).', '.intval($input->getOption('chunk')).';')->fetchAll(\PDO::FETCH_OBJ);
		$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($orders));
		foreach($orders as &$o){
			$customer = $this->customer->create()->setWebsiteId($this->store->getStore()->getWebsiteId())->loadByEmail($o->customer_email);
			try {
				$customer = $this->customerRepo->getById($customer->getEntityId());
			} catch(\Exception $e){
				$log = fopen(dirname(__FILE__).'/orders.log', 'a+');
				fwrite($log, 'Can\'t find customer: '.$o->customer_email.PHP_EOL);
				fclose($log);
				unset($customer);
				$progress->advance();
				continue;
			}
			if(!$customer->getId()){
				unset($customer);
				$progress->advance();
				continue;
			}
			$items = $this->db->query('
				SELECT 
				`od`.`product_id`, 
				`od`.`price`, 
				`p`.`number` AS `sku`, 
				`od`.`quantity` 
				FROM `gs_order_detail`  AS `od` LEFT JOIN `gs_product` AS `p` ON `p`.`id` = `od`.`product_id` WHERE `od`.`order_id` = '.$o->id.';')->fetchAll(\PDO::FETCH_OBJ);
			$order = $this->order->create()->getCollection()->addFieldToFilter('increment_id', $helper->getIncrementId($o->id))->getFirstItem();
			$id = $order->getId();
			if(!$id){
				$id = $helper->create($o, $items, $customer);
				if($id){
					if($o->status == 'pending'){
						$helper->invoice($id);
					}
					if($o->status == 'shipped'){
						$helper->invoice($id);
						$helper->ship($id, $o);
					}
					if($o->status == 'canceled' || $o->status == 'problem' || $o->status == 'voided'){
						$helper->cancel($id);
					}
				}
			} else {
				if($o->shipper == 'UPS'){
					$order->setShippingMethod('ups_'.$o->shipper_service_code);
					$order->setShippingDescription('UPS - UPS '.$o->shipper_service);
				}
				$order->setDeliveryDate(date('Y-m-d H:i:s', strtotime($o->wh_req_delivery_date)));
				$order->setBaseGrandTotal($order->getBaseSubtotal() + $order->getBaseShippingAmount());
				$order->setGrandTotal($order->getSubtotal() + $order->getShippingAmount());
				$order->setCreatedAt(date('Y-m-d H:i:s', strtotime($o->date_ordered)));
				$order->save();
			}
			if($id){
				if($o->status == 'pending'){
					$helper->invoice($id);
				}
				if($o->status == 'shipped'){
					$helper->invoice($id);
					$helper->ship($id, $o);
				}
				if($o->status == 'canceled' || $o->status == 'problem' || $o->status == 'voided'){
					$helper->cancel($id);
				}
			}
			unset($customer);
			unset($items);
			unset($order);
			unset($o);
			$progress->advance();
		}
		$progress->finish();
	}

}