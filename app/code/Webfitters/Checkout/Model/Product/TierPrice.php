<?php
namespace Webfitters\Checkout\Model\Product;

class TierPrice extends \Magento\Catalog\Model\Product\Attribute\Backend\Tierprice {

    protected function getAdditionalFields($objectArray){
        $fields = parent::getAdditionalFields($objectArray);
        $fields['value_lb'] = isset($objectArray['value_lb'])?$objectArray['value_lb']:null;
        return $fields;
    }

    protected function updateValues(array $valuesToUpdate, array $oldValues) {
        $isChanged = false;
        foreach ($valuesToUpdate as $key => $value) {
            if ((!empty($value['value']) && $oldValues[$key]['price'] != $value['value'])
                || $this->getPercentage($oldValues[$key]) != $this->getPercentage($value)
                || ($oldValues[$key]['value_lb'] != $value['value_lb'])
            ) {
                $price = new \Magento\Framework\DataObject(
                    [
                        'value_id' => $oldValues[$key]['price_id'],
                        'value' => $value['value'],
                        'percentage_value' => $this->getPercentage($value),
                        'value_lb' => $value['value_lb']
                    ]
                );
                $this->_getResource()->savePriceData($price);

                $isChanged = true;
            }
        }
        return $isChanged;
    }

    private function getPercentage($priceRow) {
        return isset($priceRow['percentage_value']) && is_numeric($priceRow['percentage_value'])
            ? $priceRow['percentage_value']
            : null;
    }

}