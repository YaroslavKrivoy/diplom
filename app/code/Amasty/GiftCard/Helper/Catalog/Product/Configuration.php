<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Helper\Catalog\Product;

use Amasty\GiftCard\Model\GiftCard;
use Magento\Catalog\Helper\Product\Configuration\ConfigurationInterface;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Configuration extends AbstractHelper implements ConfigurationInterface
{
    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig
    ) {
        parent::__construct($context);
        $this->productConfig = $productConfig;
    }

    public function getOptions(ItemInterface $item)
    {
        return $this->productConfig->getCustomOptions($item);
    }
}
