<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items\Type;

use \Magento\Sales\Block\Adminhtml\Items\AbstractItems;

/**
 * Class AbstractType
 */
class AbstractType extends AbstractItems
{
    const CALCULATE_CHILD = 0;
    const CALCULATE_PARENT = 1;

    /**
     * @var null|\Magento\Sales\Model\Order
     */
    protected $order = null;

    /**
     * @var null|\Magento\Sales\Model\Order\Item
     */
    protected $orderItem = null;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $adminHelper;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $itemResource
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $itemResource,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
        $this->adminHelper = $adminHelper;
        $this->helperData = $helperData;
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return $this
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;
        return $this;
    }

    /**
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getPriceDataObject()
    {
        return $this->getOrderItem();
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return void
     */
    public function initItem($item)
    {
        $type = $item->getProductType();
        $this->getItemRenderer($type);
    }

    /**
     * @param string $priceType
     * @return string
     */
    public function getPriceHtml($priceType)
    {
        $basePrice = $this->getOrderItem()->getData('base_' . $priceType);
        $price = $this->getOrderItem()->getData($priceType);

        return $this->adminHelper->displayPrices(
            $this->getOrder(),
            $basePrice,
            $price,
            false,
            '<br/>'
        );
    }

    /**
     * @param string $priceType
     * @return string
     */
    public function getPrice($priceType)
    {
        $price = $this->getOrderItem()->getData($priceType);
        return $this->helperData->roundAndFormatPrice($price);
    }

    /**
     * @param string $percentType
     * @return string
     */
    public function getPercent($percentType)
    {
        $percent = $this->getOrderItem()->getData($percentType);
        return number_format($percent, 2, '.', '');
    }

    /**
     * @param string $percentType
     * @return string
     */
    public function getPercentHtml($percentType)
    {
        return $this->getPercent($percentType) . "%";
    }

    /**
     * @return string
     */
    public function getItemTotalHtml()
    {
        $basePrice = $this->getBaseItemTotal();
        $price = $this->getItemTotal();

        return $this->adminHelper->displayPrices(
            $this->getOrder(),
            $basePrice,
            $price,
            false,
            '<br/>'
        );
    }

    /**
     * @return string
     */
    public function getBaseItemTotal()
    {
        $orderItem = $this->getOrderItem();

        $total = $orderItem->getBaseRowTotal()
            + $orderItem->getBaseTaxAmount()
            + $orderItem->getBaseWeeeTaxAppliedRowAmount()
            + $orderItem->getBaseDiscountTaxCompensationAmount()
            - $orderItem->getBaseDiscountAmount();

        return $this->helperData->roundAndFormatPrice($total);
    }

    /**
     * @return string
     */
    public function getItemTotal()
    {
        $orderItem = $this->getOrderItem();

        $total = $orderItem->getRowTotal()
            + $orderItem->getTaxAmount()
            + $orderItem->getWeeeTaxAppliedRowAmount()
            + $orderItem->getDiscountTaxCompensationAmount()
            - $orderItem->getDiscountAmount();

        return $this->helperData->roundAndFormatPrice($total);
    }

    /**
     * @param null $orderItem
     * @return float|int
     */
    public function getItemQty($orderItem = null)
    {
        if ($orderItem == null) {
            $orderItem = $this->getOrderItem();
        }

        $itemQty = $orderItem->getQtyOrdered()
            - $orderItem->getQtyRefunded()
            - $orderItem->getQtyCanceled();

        return $itemQty < 0 ? 0 : $itemQty;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return bool
     */
    public function isChildCalculated($item)
    {
        $options = $item->getProductOptions();
        return ($options
            && isset($options['product_calculations'])
            && $options['product_calculations'] == self::CALCULATE_CHILD
        );
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        return true;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigureButtonHtml()
    {
        $product = $this->getOrderItem()->getProduct();
        if (!$product) {
            return '';
        }

        $options = ['label' => __('Configure')];
        if ($product->canConfigure()) {
            $id = $this->getPrefixId() . $this->getOrderItem()->getId();
            $options['class'] = sprintf("configure-order-item item-id-%s", $id);

            return $this->getLayout()
                ->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData($options)
                ->setDataAttribute(['order-item-id' => $id])
                ->toHtml();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getOptionsForProduct()
    {
        $options = $this->getChildBlock('ordereditor_order_item_options');

        if ($options) {
            $options->setOrderItem($this->getOrderItem());
            return $options->toHtml();
        }

        return '';
    }

    /**
     * @return int
     */
    public function getDefaultBackToStock()
    {
        return $this->helperData->getReturnToStock();
    }

    /**
     * @return string
     */
    public function getOrderItemId()
    {
        return $this->getPrefixId() . $this->getOrderItem()->getItemId();
    }

    /**
     * @return string
     */
    public function getParentItemId()
    {
        $parentItem = $this->getOrderItem()->getParentItem();
        $parentId = !empty($parentItem) ? $parentItem->getItemId() : 0;

        return $this->getPrefixId() . $parentId;
    }

    /**
     * @return bool
     */
    public function hasOrderItemParent()
    {
        $parentItem = $this->getOrderItem()->getParentItem();
        return !empty($parentItem);
    }

    /**
     * @return string
     */
    public function getPrefixId()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getEditedItemType()
    {
        return 'order';
    }

    /**
     * @return bool
     */
    public function getCanDeleteItem()
    {
        $item = $this->getOrderItem();
        if ($item->getQtyRefunded() == $item->getQtyOrdered()) {
            return false;
        }
        return true;
    }
}
