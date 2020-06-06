<?php
namespace Webfitters\Reporting\Block\Adminhtml;

class Sales extends \Magento\Backend\Block\Template {

	protected $order;
	protected $customer;
	protected $connection;
	protected $affiliate;
	protected $agroup;
	protected $item;
	protected $request;
	protected $attribute;
	protected $startDate;
	protected $endDate;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Customer\Model\Customer $customer,
		\Magento\Framework\App\ResourceConnection $connection,
		\Magento\Sales\Model\Order $order,
		\Magento\Sales\Model\Order\Item $item,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute,
		\Mageplaza\Affiliate\Model\Group $agroup,
		\Magento\Framework\App\Request\Http $request,
		\Mageplaza\Affiliate\Model\Account $affiliate,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->order = $order;
		$this->customer = $customer;
		$this->connection = $connection;
		$this->affiliate = $affiliate;
		$this->agroup = $agroup;
		$this->item = $item;
		$this->request = $request;
		$this->attribute = $attribute;
		$this->startDate = ($this->request->get('start_date'))?\Carbon\Carbon::parse($this->request->get('start_date'))->startOfDay():\Carbon\Carbon::now()->startOfMonth()->startOfDay();
		$this->endDate = ($this->request->get('end_date'))?\Carbon\Carbon::parse($this->request->get('end_date'))->endOfDay():\Carbon\Carbon::now()->endOfDay();
	}

	public function getTotalOrderCount(){
		return $this->getOrderCollection()->count();
	}

	public function getTotalOrderValue(){
		return $this->getOrderCollection()->addExpressionFieldToSelect('total', 'SUM(base_subtotal)', 'total')->getFirstItem()->getTotal();
	}

	public function getTotalOrderAvg(){
		$count = $this->getTotalOrderCount();
		if($count == 0){
			return $count;
		}
		return $this->getTotalOrderValue() / $this->getTotalOrderCount();
	}

	public function getGrossToRepeat(){
		$repeats = $this->connection->getConnection()->query(
			$this->customer->getCollection()->getSelect()->columns([
				'id' => 'e.entity_id'
			])
			->join(['sales_order'], 'e.entity_id = sales_order.customer_id', [])
			->group('sales_order.customer_id')
			->having('COUNT(sales_order.customer_id) > 1')
		)->fetchAll(\PDO::FETCH_OBJ);
		$ids = [];
		foreach($repeats as $repeat){
			$ids[] = $repeat->entity_id;
		}
		if(count($ids) == 0){
			return (object)[
				'total' => 0,
				'sales' => 0
			];
		}
		$rows = $this->connection->getConnection()->query(
			$this->getOrderCollection()->getSelect()->columns([
				'total' => 'COUNT(main_table.entity_id)',
				'sales' => 'SUM(main_table.base_subtotal)'
			])
			->where('main_table.customer_id IN ('.implode(',', $ids).')')
		)->fetchAll(\PDO::FETCH_OBJ);
		if(count($rows)>0){
			return $rows[0];
		} 
		return (object)[
			'total' => 0,
			'sales' => 0
		];
	}

	public function getReferralSales(){
		$rows = $this->connection->getConnection()->query(
			$this->getOrderCollection()->getSelect()->columns([
				'total' => 'COUNT(*)',
				'sales' => 'SUM(main_table.base_subtotal)'
			])
			->join(['mageplaza_affiliate_account'], 'main_table.affiliate_key = mageplaza_affiliate_account.code', [])
			->join(['customer_entity'], 'customer_entity.entity_id = mageplaza_affiliate_account.customer_id', [])
			->join(['mageplaza_affiliate_group'], 'mageplaza_affiliate_group.group_id = mageplaza_affiliate_account.group_id', [])
			->where('mageplaza_affiliate_group.name = "General"')
		)->fetchAll(\PDO::FETCH_OBJ);
		if(count($rows)>0){
			return $rows[0];
		}
		return (object)[
			'total' => 0,
			'sales' => 0
		];
	}

	public function getReferrerSales(){

	}

	public function getAffiliateOrders(){
		$rows = $this->connection->getConnection()->query(
			$this->getOrderCollection()->getSelect()->columns([
				'sales' => 'SUM(main_table.base_subtotal)',
				'first_name' => 'customer_entity.firstname',
				'last_name' => 'customer_entity.lastname'
			])
			->join(['mageplaza_affiliate_account'], 'main_table.affiliate_key = mageplaza_affiliate_account.code', [])
			->join(['customer_entity'], 'customer_entity.entity_id = mageplaza_affiliate_account.customer_id', [])
			->join(['mageplaza_affiliate_group'], 'mageplaza_affiliate_group.group_id = mageplaza_affiliate_account.group_id', [])
			->where('mageplaza_affiliate_group.name != "General"')
			->group('mageplaza_affiliate_account.customer_id')
		)->fetchAll(\PDO::FETCH_OBJ);
		return $rows;
	}

	public function getOrdersBySpecial(){
		return $this->connection->getConnection()->query(
			$this->getItemCollection()
			->columns([
				'main_table.*',
				'sales' => 'main_table.base_row_total',
				'product' => 'main_table.name'
			])
			->join(['customer_entity'], 'customer_entity.entity_id = sales_order.customer_id', [])
			->join(['catalogrule_product_price'], 'catalogrule_product_price.product_id = main_table.product_id', [])
			->where('
				catalogrule_product_price.rule_price IS NOT NULL 
				AND catalogrule_product_price.rule_date >= "'.$this->startDate->toDateTimeString().'"
				AND catalogrule_product_price.rule_date <= "'.$this->endDate->toDateTimeString().'"
				AND catalogrule_product_price.website_id = 1
			')
			->group('main_table.product_id')
			->order('sales desc')->limit(10, 0)
		)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getCustomerReorders(){
		$repeats = $this->connection->getConnection()->query(
			$this->customer->getCollection()->getSelect()->columns([
				'id' => 'e.entity_id'
			])
			->join(['sales_order'], 'e.entity_id = sales_order.customer_id', [])
			->group('sales_order.customer_id')
			->having('COUNT(sales_order.customer_id) > 1')
		)->fetchAll(\PDO::FETCH_OBJ);
		$ids = [];
		foreach($repeats as $repeat){
			$ids[] = $repeat->entity_id;
		}
		if(count($ids) == 0){
			return [];
		}
		$rows = $this->connection->getConnection()->query(
			$this->getOrderCollection()->getSelect()
			->where('main_table.customer_id IN ('.implode(',', $ids).')')
		)->fetchAll(\PDO::FETCH_OBJ);
		$customers = [];
		$return = [];
		foreach($rows as $row){
			if(!isset($customers[$row->customer_id])){
				$customers[$row->customer_id] = (object)[
					'orders' => 0,
					'sales' => 0
				];
			}
			$customers[$row->customer_id]->orders++;
			$customers[$row->customer_id]->sales += $row->base_subtotal;
		}
		foreach($customers as $cust){
			if(!isset($return[$cust->orders])){
				$return[$cust->orders] = (object)[
					'sales' => 0,
					'customers' => 0,
					'total' => 0
				];
			}
			$return[$cust->orders]->sales += $cust->sales;
			$return[$cust->orders]->total += $cust->orders;
			$return[$cust->orders]->customers++;
		}
		return $return;
	}

	public function getShippingSales(){
		return $this->connection->getConnection()->query(
			$this->getOrderCollection()->getSelect()->columns([
				'main_table.*',
				'sales' => 'SUM(main_table.base_subtotal)'
			])
			->group('main_table.shipping_method')
			->order('sales desc')->limit(10, 0)
		)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getOrdersByState(){
		return $this->connection->getConnection()->query(
			$this->getItemCollection()
				->columns([
					'main_table.*',
					'sales' => 'SUM(main_table.base_row_total)',
					'state' => 'sales_order_address.region'
				])
				->join(['sales_order_address'], 'sales_order_address.parent_id = sales_order.entity_id', [])
				->where('sales_order_address.address_type = "shipping"')
				->group('sales_order_address.region')
				->order('sales desc')->limit(10, 0)
		)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function getOrdersBySpecies(){
		return $this->connection->getConnection()->query(
			$this->getItemCollection()
				->columns([
					'main_table.*',
					'sales' => 'SUM(main_table.base_row_total)', 
					'species' => 'eav_attribute_option_value.value'
				])
				->join(['catalog_product_entity_int'], 'main_table.product_id = catalog_product_entity_int.entity_id', [])
				->where('catalog_product_entity_int.attribute_id = '.$this->attribute->getIdByCode('catalog_product', 'species'))
				->where('catalog_product_entity_int.store_id = 0')
				->join(['eav_attribute_option_value'], 'catalog_product_entity_int.value = eav_attribute_option_value.option_id', [])
				->group('eav_attribute_option_value.option_id')
				->order('sales desc')->limit(10, 0)
			)
		->fetchAll(\PDO::FETCH_OBJ);
	}

	protected function getItemCollection(){
		$query = $this->item->getCollection()->getSelect()
				->join(['sales_order'], 'main_table.order_id = sales_order.entity_id', [])
				->where('sales_order.created_at >= "'.$this->startDate->toDateTimeString().'"')
				->where('sales_order.created_at <= "'.$this->endDate->toDateTimeString().'"')
				->where('sales_order.status = "complete"');
		if($this->request->get('type') && $this->request->get('type') == 'wholesale'){
			$query->where('sales_order.customer_group_id NOT IN (0,1,4)');
		}
		if($this->request->get('type') && $this->request->get('type') == 'retail'){
			$query->where('sales_order.customer_group_id IN (0,1,4)');
		}
		return $query;
	}

	protected function getOrderCollection(){
		$query = $this->order->getCollection()->addFieldToFilter('main_table.status', 'complete')->addFieldToFilter('main_table.created_at', [
			'gteq' => $this->startDate->toDateTimeString(), 
			'lteq' => $this->endDate->toDateTimeString()
		]);
		if($this->request->get('type') && $this->request->get('type') == 'wholesale'){
			$query->addFieldToFilter('main_table.customer_group_id', ['nin' => [0,1,4]]);
		}
		if($this->request->get('type') && $this->request->get('type') == 'retail'){
			$query->addFieldToFilter('main_table.customer_group_id', ['in' => [0,1,4]]);
		}
		return $query;
	}

	public function getRequest(){
		return $this->request;
	}

	public function getStartDate(){
		return $this->startDate;
	}

	public function getEndDate(){
		return $this->endDate;
	}

}