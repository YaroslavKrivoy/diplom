<?php
namespace Webfitters\Checkout\Plugin\Quote\Item;

class UpdateQtyWeight {

	protected $product;
	protected $connection;
	protected $order;

	public function __construct(
		\Magento\Catalog\Model\ProductFactory $product,
		\Magento\Framework\App\ResourceConnection $connection,
		\Magento\Sales\Model\AdminOrder\Create $order
	){
		$this->product = $product;
		$this->connection = $connection;
		$this->order = $order;
	}

	public function afterUpdate(
		\Magento\Quote\Model\Quote\Item\Updater $updater, 
		\Magento\Quote\Model\Quote\Item\Updater $result, 
		\Magento\Quote\Model\Quote\Item $item, 
		array $info
	) {
		$data = new \Magento\Framework\DataObject($info);
		if($data->getQtyWeight() && floatval($data->getQtyWeight()) > 0){
			$product = $this->product->create()->load($item->getProduct()->getId());
			$price = floatval($product->getPriceLb());
			$prices = $this->connection->getConnection()->fetchAll('SELECT * FROM `'.$this->connection->getTableName('catalog_product_entity_tier_price').'` WHERE `entity_id` = '.$product->getId().' AND `customer_group_id` = '.$this->order->getQuote()->getCustomerGroupId());
			if(count($prices) > 0 && floatval($prices[0]['value_lb']) > 0) {
				$price = floatval($prices[0]['value_lb']);
			}
			$qty = $data->getQty();
            $data->setQty(1);
            $item->setQty(1);
            $item->setQtyWeight($data->getQtyWeight());
            $item->setCaseQty($qty);
            $item->setCustomPrice($price * $data->getQtyWeight());
            $item->setOriginalCustomPrice($price * $data->getQtyWeight());
            $item->getProduct()->setIsSuperMode(true);
            $item->save();
        } elseif($item->getQtyWeight() > 0) {
            $item->setQtyWeight(null);
            $item->setCaseQty(null);
            $item->setCustomPrice(null);
            $item->setOriginalCustomPrice(null);
            $item->getProduct()->setIsSuperMode(false);
        }
	}

}