<?php
namespace Webfitters\Reporting\Block\Adminhtml;

class Inventory extends \Magento\Backend\Block\Template {

	protected $order;
	protected $items;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Sales\Model\Order $order,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->order = $order;
		$this->items = null;
	}

	public function getProducts(){
		if(!$this->items){
			$this->items = [];
			$collection = $this->order->getCollection()->addFieldToFilter('status', ['in' => ['pending', 'processing', 'hold']]);
			foreach($collection as $order){
				$items = $order->getItems();
				foreach($items as $item){
					if(!isset($this->items[$item->getSku()])){
						$this->items[$item->getSku()] = (object)[
							'number' => $item->getSku(),
							'name' => $item->getName(),
							'qty' => 0,
							'weight' => 0
						];
					}
					$this->items[$item->getSku()]->qty += $item->getQtyOrdered();
					$this->items[$item->getSku()]->weight += ($item->getWeight() * $item->getQtyOrdered());
				}
			}
		}
		return $this->items;
	}
	
}