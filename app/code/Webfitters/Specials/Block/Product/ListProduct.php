<?php
namespace Webfitters\Specials\Block\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct {

	protected $attribute;
	protected $request;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attribute,
        array $data = []
    ){
    	$this->attribute = $attribute;
    	$this->request = $request;
		parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
	}

	protected function _getProductCollection() {
		if($this->_productCollection === null){
			$this->_productCollection = $this->getLayer()->getProductCollection();
			$select = $this->_productCollection->getSelect();
			$select->joinLeft(['special_from_date' => 'catalog_product_entity_datetime'], 
				'special_from_date.entity_id = e.entity_id AND
				special_from_date.attribute_id = '.$this->attribute->getIdByCode('catalog_product', 'special_from_date').' AND
				special_from_date.store_id = 0', 
			[]);
			$select->joinLeft(['special_to_date' => 'catalog_product_entity_datetime'], 
				'special_to_date.entity_id = e.entity_id AND 
				special_to_date.attribute_id = '.$this->attribute->getIdByCode('catalog_product', 'special_to_date').' AND 
				special_to_date.store_id = 0', 
			[]);
			$select->joinLeft(['special_price' => 'catalog_product_entity_decimal'], 
				'special_price.entity_id = e.entity_id AND
				special_price.attribute_id = '.$this->attribute->getIdByCode('catalog_product', 'special_price').' AND
				special_price.store_id = 0', 
			[]);
			$select->joinLeft(['catalog_rule' => 'catalogrule_product_price'], 
				'catalog_rule.product_id = e.entity_id AND catalog_rule.website_id = 1', 
			[]);
			$select->distinct('e.entity_id');
			$select->columns([
				'special_from_date' => 'special_from_date.value',
				'special_to_date' => 'special_to_date.value',
				'special_price' => 'special_price.value'
			]);
			$select->where('
				(special_from_date.value IS NULL OR special_from_date.value <= "'.date('Y-m-d 23:59:59').'") AND
				(special_to_date.value IS NULL OR special_to_date.value >= "'.date('Y-m-d 00:00:00').'") AND
				(special_price.value IS NOT NULL AND special_price.value > 0)
			');
			$select->orWhere('
				catalog_rule.rule_price IS NOT NULL 
				AND catalog_rule.rule_date >= "'.date('Y-m-d 00:00:00').'"
				AND catalog_rule.rule_date <= "'.date('Y-m-d 23:59:59').'"
			');
			$this->_productCollection->setPageSize((($this->request->getParam('product_list_limit'))?$this->request->getParam('product_list_limit'):15));
			$this->_productCollection->setCurPage((($this->request->getParam('p'))?$this->request->getParam('p'):1));
			$this->_productCollection->setOrder(
				($this->request->getParam('product_list_order'))?$this->request->getParam('product_list_order'):'name', 
				($this->request->getParam('product_list_dir'))?$this->request->getParam('product_list_dir'):'asc'
			);
	        $this->_eventManager->dispatch('catalog_block_product_list_collection', ['collection' => $this->_productCollection]);
	        $toolbar = $this->getToolbarBlock();
        	$this->configureToolbar($toolbar, $this->_productCollection);
		}
        return $this->_productCollection;
    }

    private function configureToolbar(\Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar, \Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }

}