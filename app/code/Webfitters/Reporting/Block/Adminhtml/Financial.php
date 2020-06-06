<?php
namespace Webfitters\Reporting\Block\Adminhtml;

class Financial extends \Magento\Backend\Block\Template {

	protected $order;
	protected $customer;
	protected $connection;
	protected $affiliate;
	protected $agroup;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Customer\Model\Customer $customer,
		\Magento\Framework\App\ResourceConnection $connection,
		\Magento\Sales\Model\Order $order,
		\Mageplaza\Affiliate\Model\Group $agroup,
		\Mageplaza\Affiliate\Model\Account $affiliate,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->order = $order;
		$this->customer = $customer;
		$this->connection = $connection;
		$this->affiliate = $affiliate;
		$this->agroup = $agroup;
	}

	/* Pending order functions */
	public function getTotalPendingOrders(){
		return $this->order->getCollection()->addFieldToFilter('status', ['in' => ['pending', 'processing', 'hold']])->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])->count();
	}

	public function getTotalPendingOrderAmount(){
		return $this->order->getCollection()
							->addFieldToFilter('status', ['in' => ['pending', 'processing', 'hold']])
							->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])
						->addExpressionFieldToSelect('total', 'SUM(grand_total)', 'total')->getFirstItem()->getTotal();
	}

	public function getFiveDayOrderCount(){
		return $this->order->getCollection()
					->addFieldToFilter('status', ['in' => ['pending', 'processing', 'hold']])
					->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])
					->addFieldToFilter('created_at', ['lteq' => date('Y-m-d H:i:s', strtotime('-5 days'))])
				->count();
	}

	public function getTenDayOrderCount(){
		return $this->order->getCollection()
					->addFieldToFilter('status', ['in' => ['pending', 'processing', 'hold']])
					->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])
					->addFieldToFilter('created_at', ['lteq' => date('Y-m-d H:i:s', strtotime('-10 days'))])
				->count();
	}

	public function getFifteenDayOrderCount(){
		return $this->order->getCollection()
					->addFieldToFilter('status', ['in' => ['pending', 'processing', 'hold']])
					->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])
					->addFieldToFilter('created_at', ['lteq' => date('Y-m-d H:i:s', strtotime('-15 days'))])
				->count();
	}
	
	/* Customer functions */
	public function getCustomerCount(){
		return $this->customer->getCollection()->getSize();
	}

	public function getCustomerPurchasers(){
		$sql = $this->customer->getCollection()->getSelect()->join(['sales_order'], 'e.entity_id = sales_order.customer_id', [])->group('sales_order.customer_id')->having('COUNT(sales_order.customer_id) = 1');
		return count($this->connection->getConnection()->query($sql)->fetchAll());
	}

	public function getCustomerRepeatPurchasers(){
		$sql = $this->customer->getCollection()->getSelect()->join(['sales_order'], 'e.entity_id = sales_order.customer_id', [])->group('sales_order.customer_id')->having('COUNT(sales_order.customer_id) > 1');
		return count($this->connection->getConnection()->query($sql)->fetchAll());
	}

	/* Shipment functions */
	public function getShipmentCount($start = null, $end = null){
		$end = (($end)?$end:date('Y-m-d H:i:s'));
		$query =  $this->order->getCollection()->addFieldToFilter('status', 'complete')
					->addExpressionFieldToSelect('amount', 'COUNT(main_table.entity_id)', 'amount')->getSelect()
					->join(['sales_shipment'], 'main_table.entity_id = sales_shipment.order_id', []);
		if($start){
			$query->where('sales_shipment.created_at >= "'.$start.'"');
		}
		$query->where('sales_shipment.created_at <= "'.$end.'"')
		->having('COUNT(sales_shipment.entity_id) >= 1')
		->group('sales_shipment.order_id');
		return $this->connection->getConnection()->query($query)->fetch()['amount'];
	}

	public function getShipmentAmount($start = null, $end = null){
		$end = (($end)?$end:date('Y-m-d H:i:s'));
		$query =  $this->order->getCollection()->addFieldToFilter('status', 'complete')
					->addExpressionFieldToSelect('total', 'SUM(grand_total)', 'total')->getSelect()
					->join(['sales_shipment'], 'main_table.entity_id = sales_shipment.order_id', []);
		if($start){
			$query->where('sales_shipment.created_at >= "'.$start.'"');
		}
		$query->where('sales_shipment.created_at <= "'.$end.'"')
		->having('COUNT(sales_shipment.entity_id) >= 1')
		->group('sales_shipment.order_id');
		return $this->connection->getConnection()->query($query)->fetch()['total'];
	}

	/* Affiliate functions */
	public function getRegisteredAffiliates(){
		$default = $this->agroup->getCollection()->addFieldToFilter('name', 'General')->getFirstItem()->getGroupId();
		return $this->affiliate->getCollection()->addFieldToFilter('group_id', ['neq' => $default])->addFieldToFilter('status', 1)->count();
	}

	public function getTotalOrdersToday(){
		return $this->order->getCollection()->addFieldToFilter('created_at', ['gteq' => date('Y-m-d 00:00:00')])->count();
	}

	public function getTotalOrdersAmountToday(){
		return $this->order->getCollection()->addFieldToFilter('created_at', ['gteq' => date('Y-m-d 00:00:00')])->addExpressionFieldToSelect('total', 'SUM(grand_total)', 'total')->getFirstItem()->getTotal();
	}

	public function getCancelledOrders(){
		return $this->order->getCollection()->addFieldToFilter('status', ['in' => ['cancelled', 'closed']])->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])->count();
	}

	public function getCancelledOrdersAmount(){
		return $this->order->getCollection()
							->addFieldToFilter('status', ['in' => ['cancelled', 'closed']])
							->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', strtotime('-30 days'))])
						->addExpressionFieldToSelect('total', 'SUM(grand_total)', 'total')->getFirstItem()->getTotal();
	}

}