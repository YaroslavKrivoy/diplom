<?php
namespace Webfitters\Custom\Model\ResourceModel\Order\Customer;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Customer\Collection {

    protected function _initSelect() {
        parent::_initSelect();
        $this->joinField(
            'customer_group_name',
            'customer_group',
            'customer_group_code',
            'customer_group_id=group_id',
            null,
            'left'
        );
        /*$this->joinField(
            'company',
            'customer_address_entity',
            'company',
            'entity_id = default_billing',
            null,
            'inner'
        );*/
        return $this;
    }

}