<?php
namespace MageArray\StorePickup\Model\Checkout;

class DefaultConfigProvider
{
    public function __construct(
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \MageArray\StorePickup\Model\StoreFactory $storeFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageArray\StorePickup\Helper\Data $dataHelper
    ) {
        $this->regionFactory = $regionFactory;
        $this->_storeFactory = $storeFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result)
    {
        $enable = $this->dataHelper->isEnabled();
        $methodproductWise = $this->dataHelper->showMethodProductWise();
        $result["storePickup"] = "";
        $result["storeList"] = "";
        $result["enableStore"] = $enable;
        $result["storeProductWise"] = $methodproductWise;
        if ($enable == 1) {
            $storeArr = [];
            $storeColl = $this->_storeFactory->create()->getCollection();
            foreach ($storeColl as $storeDetail) {
                $sId = $storeDetail->getStorepickupId();
                $regionColl = $this->regionFactory->create()->getCollection()
                    ->addFieldToFilter("default_name", $storeDetail->getState());
                $regionId = "";
                $regionCode = "";
                if (count($regionColl->getData()) > 0) {
                    foreach ($regionColl as $region) {
                        $regionId = $region->getRegionId();
                        $regionCode = $region->getCode();
                    }
                }
                $storename = $this->dataHelper->getStoreName($storeDetail->getStoreName());
                $storeArr[$sId]["firstname"] = $storename["firstname"];
                $storeArr[$sId]["lastname"] = $storename["lastname"];
                $storeArr[$sId]["phone_number"] = $storeDetail->getPhoneNumber();
                $storeArr[$sId]["address"] = $storeDetail->getAddress();
                $storeArr[$sId]["city"] = $storeDetail->getCity();
                $storeArr[$sId]["zipcode"] = $storeDetail->getZipcode();
                $storeArr[$sId]["country"] = $storeDetail->getCountry();
                $storeArr[$sId]["state"] = $storeDetail->getState();
                $storeArr[$sId]["region_id"] = $regionId;
                $storeArr[$sId]["region_code"] = $regionCode;
            }
            $storeColl->addFieldToFilter(
                'store_view_ids',
                [
                    ['finset'=> ['0']],
                    ['finset'=> [$this->getStoreId()]],
                ]
            );
            $storeColl->setOrder('sort_order', 'ASC');
            $result["storePickup"] = $storeArr;
            $result["storeList"] = $this->jsonHelper->jsonEncode($storeColl->getData());
        }
        return $result;
    }
}
