<?php 
namespace Webfitters\CustomerCompany\Plugin;
 
class SalesOrderCompany {
    
    protected $collection;
    protected $invoice;
    protected $shipment;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\Grid\Collection $invoice,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Grid\Collection $shipment
    ) {
        $this->collection = $collection;
        $this->invoice = $invoice;
        $this->shipment = $shipment;
    }

    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source' && $result instanceof $this->collection) {
            $select = $this->collection->getSelect();
            $select->joinLeft(['cet' => $this->collection->getTable('customer_entity_text')], 'cet.entity_id = main_table.customer_id', ['value' => 'value']);
            $select->joinLeft(['eav' => $this->collection->getTable('eav_attribute')], 'cet.attribute_id = eav.attribute_id', []);
            $select->where('(eav.attribute_code = "company" AND eav.entity_type_id = 1) OR cet.entity_id IS NULL');  
            return $this->collection;
        }
        if($requestName == 'sales_order_invoice_grid_data_source' && $result instanceof $this->invoice) {
            $select = $this->invoice->getSelect();
            $select->joinLeft(['cet' => $this->collection->getTable('customer_entity_text')], 'cet.entity_id = main_table.customer_id', ['value' => 'value']);
            $select->joinLeft(['eav' => $this->collection->getTable('eav_attribute')], 'cet.attribute_id = eav.attribute_id', []);
            $select->where('(eav.attribute_code = "company" AND eav.entity_type_id = 1) OR cet.entity_id IS NULL');  
            return $this->invoice;
        }
        if($requestName == 'sales_order_shipment_grid_data_source' && $result instanceof $this->shipment) {
            $select = $this->invoice->getSelect();
            $select->joinLeft(['cet' => $this->collection->getTable('customer_entity_text')], 'cet.entity_id = main_table.customer_id', ['value' => 'value']);
            $select->joinLeft(['eav' => $this->collection->getTable('eav_attribute')], 'cet.attribute_id = eav.attribute_id', []);
            $select->where('(eav.attribute_code = "company" AND eav.entity_type_id = 1) OR cet.entity_id IS NULL');  
            return $this->invoice;
        }
        return $result;
    }
}