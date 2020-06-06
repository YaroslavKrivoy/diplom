<?php
namespace Webfitters\CustomerCompany\Ui\Component\Listing\Column;
 
class Company extends \Magento\Ui\Component\Listing\Columns\Column {
    
    protected $customer;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $component,
        \Magento\Customer\Model\CustomerFactory $customer,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $component, $components, $data);
        $this->customer = $customer;
    }
 
    public function prepareDataSource(array $dataSource) {
        /*$customers = [];
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $customers[] = $item['customer_id'];
            }
        }
        if(count($customers) > 0){
            $loaded = $this->customer->create()->getCollection()->addFieldToFilter('entity_id', ['in' => $customers])->addFieldToSelect('company', 'entity_id');
            $customers = [];
            foreach($loaded as $customer){
                $customers[$customer->getId()] = $customer->getCompany();
            }
            if (isset($dataSource['data']['items'])) {
                foreach ($dataSource['data']['items'] as &$item) {
                    $item[$this->getData('name')] = (isset($customers[$item['customer_id']]))?$customers[$item['customer_id']]:'';
                }
            }
        }*/
        return $dataSource;
    }

}