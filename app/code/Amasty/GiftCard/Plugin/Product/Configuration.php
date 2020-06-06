<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Product;

use Amasty\GiftCard\Model\Product\Type\GiftCard;

class Configuration
{
    /**
     * @var \Amasty\GiftCard\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    private $currentItem;

    /**
     * @var \Amasty\GiftCard\Model\Options
     */
    private $options;

    public function __construct(
        \Amasty\GiftCard\Model\Config $config,
        \Amasty\GiftCard\Model\Options $options
    ) {
        $this->config = $config;
        $this->options = $options;
    }

    /**
     * @param \Magento\Catalog\Helper\Product\Configuration $subject
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
     */
    public function beforeGetCustomOptions(
        \Magento\Catalog\Helper\Product\Configuration $subject,
        \Magento\Catalog\Model\Product\Configuration\Item\ItemInterface $item
    ) {
        $this->currentItem = $item;
    }

    /**
     * @param \Magento\Catalog\Helper\Product\Configuration $configuration
     * @param $result
     * @return mixed
     */
    public function afterGetCustomOptions(
        \Magento\Catalog\Helper\Product\Configuration $configuration,
        $result
    ) {
        if ($this->currentItem->getProduct()->getTypeId() === GiftCard::TYPE_GIFTCARD_PRODUCT
            && $this->config->getScopeValue('display_options/show_options_in_cart_checkout')
        ) {
            $giftOptions = $this->currentItem->getBuyRequest()->getData();
            $result = array_merge($this->options->getGiftOptions($giftOptions), $result);
        }

        return $result;
    }
}
