<?php
namespace MageWorx\OrderEditor\Model\Order\Invoice;

class Item extends \Magento\Sales\Model\Order\Invoice\Item {
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $orderItemFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function setOrderItem(\Magento\Sales\Model\Order\Item $item = null) {
        if(!$item){
            return $this;
        }
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
        return $this;
    }

}