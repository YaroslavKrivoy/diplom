<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Form\Items;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\Product\Type;
use Magento\Tax\Model\Config;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \MageWorx\OrderEditor\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateInterface $stockState
     * @param \MageWorx\OrderEditor\Helper\Data $helperData
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        \MageWorx\OrderEditor\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $wishlistFactory,
            $giftMessageSave,
            $taxConfig,
            $taxData,
            $messageHelper,
            $stockRegistry,
            $stockState,
            $data
        );
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
     * Get items
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->helperData->getOrder()->getItems();
    }

    /**
     * @return string
     */
    public function getItemsForm()
    {
        $orderItems = $this->getItems();
        $formHtml = "";
        $i = 0;

        foreach ($orderItems as $item) {
            $productType = $item->getProductType();
            if ($item->getParentItemId() != null) {
                continue;
            }

            $evenOdd = ($i%2) ? 'even' : 'odd';
            $formHtml .= '<tbody class="'.$evenOdd.'">';
            switch ($productType) {
                case Type::TYPE_BUNDLE:
                    $formHtml .= $this->getBundleForm($item);
                    break;
                default:
                    $formHtml .= $this->getSimpleForm($item);
            }
            $formHtml .= '</tbody>';
            $i++;
        }

        return $formHtml;
    }

    /**
     * Get form for simple product
     *
     * @param \MageWorx\OrderEditor\Model\Order\Item $orderItem
     * @throws \Exception
     * @return string
     */
    public function getSimpleForm($orderItem)
    {
        $itemForm = $this->getChildBlock('ordereditor_order_simple_item_form');
        if (!$itemForm) {
            throw new \Exception('Can not load order item');
        }

        return $this->getItemFormHtml($itemForm, $orderItem);
    }

    /**
     * Get form for bundle product
     *
     * @param \MageWorx\OrderEditor\Model\Order\Item $orderItem
     * @throws \Exception
     * @return string
     */
    public function getBundleForm($orderItem)
    {
        $itemForm = $this->getChildBlock('ordereditor_order_bundle_item_form');
        if (!$itemForm) {
            throw new \Exception('Can not load order item');
        }

        $formHtml = $this->getItemFormHtml($itemForm, $orderItem);

        $childItems = $orderItem->getChildrenItems();
        $_prevOptionId = '';

        foreach ($childItems as $item) {
            $attributes = $this->getSelectionAttributes($item);
            if ($item->getParentItem() && $_prevOptionId != $attributes['option_id']) {
                $item->setOptionLabel($attributes['option_label']);
                $_prevOptionId = $attributes['option_id'];
            }

            $formHtml .= $this->getSimpleForm($item);
        }

        return $formHtml;
    }

    /**
     * Get item's form html
     *
     * @param \MageWorx\OrderEditor\Model\Order\Item $orderItem
     * @param mixed $itemForm
     * @return string
     */
    protected function getItemFormHtml($itemForm, $orderItem)
    {
        if ($itemForm->getEditedItemType() == 'order') {
            $itemForm->setOrder($this->helperData->getOrder());
        } else {
            $itemForm->setOrder($this->getOrder());
        }
        $itemForm->setOrderItem($orderItem);
        return $itemForm->toHtml();
    }

    /**
     * @return mixed|null
     */
    protected function getSelectionAttributes($item)
    {
        $options = $item->getProductOptions();
        if (isset($options['bundle_selection_attributes'])) {
            return $this->helperData->decodeBuyRequestValue($options['bundle_selection_attributes']);
        }
        return null;
    }

    /**
     * Get tax config data json
     *
     * @return string
     */
    public function getJsonTaxConfigParams()
    {
        $data = [
            'calcCatalogPricesInclTax' => (int)$this->_taxConfig->priceIncludesTax() ? 1 : 0,
            'calcShippingPricesInclTax' => (int)$this->_taxConfig->shippingPriceIncludesTax() ? 1 : 0,
            'taxCalculationMethod' => $this->_taxConfig->getAlgorithm(),
            'taxCalculationBasedOn' => (int)$this->getTaxCalculationBasedOn() ? 1 : 0,
            'applyTaxAfterDiscount' => (int)$this->_taxConfig->applyTaxAfterDiscount() ? 1 : 0,
            'applyDiscountOnPricesInclTax' => (int)$this->_taxConfig->discountTax() ? 1 : 0,
            'configureQuoteItemsUrl' => $this->_urlBuilder->getUrl('ordereditor/form/configureQuoteItems'),
            'configureConfirmUrl' => $this->_urlBuilder->getUrl('ordereditor/form/options'),
        ];

        return json_encode($data);
    }

    /**
     * Get tax config data json
     *
     * @return string
     */
    public function getJsonGridParams()
    {
        $params = ['order_id' => $this->helperData->getOrder()->getId()];

        $data = [
            'searchGridUrl' => $this->_urlBuilder->getUrl('ordereditor/form/search', $params),
            'addProductsUrl' => $this->_urlBuilder->getUrl('ordereditor/form/add'),
            'magentoLoadBlockUrl' => $this->_urlBuilder->getUrl('sales/order_create/loadBlock'),
            'removeQuoteItemUrl' => $this->_urlBuilder->getUrl('ordereditor/form/removeQuoteItem'),
        ];

        return json_encode($data);
    }

    protected function getTaxCalculationBasedOn()
    {
        return $this->_scopeConfig->getValue(
            Config::CONFIG_XML_PATH_BASED_ON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->helperData->getQuote();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->helperData->getCustomerId();
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->helperData->getStore();
    }

    /**
     * Retrieve store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->helperData->getStoreId();
    }
}
