<?php
namespace Webfitters\Checkout\Plugin\Product;

class TierPricing {
	
	protected $groups;
	protected $store;
	protected $config;

	public function __construct(
		\Magento\Customer\Api\GroupManagementInterface $groups,
		\Magento\Framework\App\Config\ScopeConfigInterface $config,
		\Magento\Store\Model\StoreManagerInterface $store
	){
		$this->groups = $groups;
		$this->config = $config;
		$this->store = $store;
	}

	public function aroundSetTierPrices(\Magento\Catalog\Model\Product\Type\Price $subject, \Closure $proceed, $product, $tierPrices = null){
		$proceed($product, $tierPrices);
		if ($tierPrices === null) {
            return $subject;
        }
        $allGroupsId = $this->groups->getAllCustomersGroup()->getId();
        $websiteId = $this->getWebsiteForPriceScope();
        $prices = [];
        foreach($tierPrices as $price){
        	$extensionAttributes = $price->getExtensionAttributes();
            $priceWebsiteId = $websiteId;
            if (isset($extensionAttributes) && is_numeric($extensionAttributes->getWebsiteId())) {
                $priceWebsiteId = (string)$extensionAttributes->getWebsiteId();
            }
            $prices[] = [
                'website_id' => $priceWebsiteId,
                'cust_group' => $price->getCustomerGroupId(),
                'website_price' => $price->getValue(),
                'price' => $price->getValue(),
                'all_groups' => ($price->getCustomerGroupId() == $allGroupsId),
                'price_qty' => $price->getQty(),
                'percentage_value' => $extensionAttributes ? $extensionAttributes->getPercentageValue() : null,
                'value_lb' => ($price->getValueLb() > 0)?$price->getValueLb():null
            ];
        }
        $product->setData('tier_price', $prices);
        return $subject;
	}

	protected function getWebsiteForPriceScope() {
        $websiteId = 0;
        $value = $this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
        if ($value != 0) {
            $websiteId = $this->store->getWebsite()->getId();
        }
        return $websiteId;
    }

}