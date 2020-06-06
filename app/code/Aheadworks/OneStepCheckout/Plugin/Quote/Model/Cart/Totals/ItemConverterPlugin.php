<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Plugin\Quote\Model\Cart\Totals;

use Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class ItemConverterPlugin
 *
 * @package Aheadworks\OneStepCheckout\Plugin\Quote\Model\Cart\Totals
 */
class ItemConverterPlugin
{
    /**
     * @var TotalsItemExtensionInterfaceFactory
     */
    private $totalsItemExtensionFactory;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistryInterface;

    /**
     * @param TotalsItemExtensionInterfaceFactory $totalsItemExtensionFactory
     * @param StockRegistryInterface $stockRegistryInterface
     */
    public function __construct(
        TotalsItemExtensionInterfaceFactory $totalsItemExtensionFactory,
        StockRegistryInterface $stockRegistryInterface
    ) {
        $this->totalsItemExtensionFactory = $totalsItemExtensionFactory;
        $this->stockRegistryInterface = $stockRegistryInterface;
    }

    /**
     * Set extension attributes to TotalsItem
     *
     * @param ItemConverter $subject
     * @param TotalsItemInterface $resultTotalsItem
     * @param QuoteItem $quoteItem
     * @return TotalsItemInterface
     */
    public function afterModelToDataObject($subject, $resultTotalsItem, $quoteItem)
    {
        $extensionAttributes = $resultTotalsItem->getExtensionAttributes()
            ? $resultTotalsItem->getExtensionAttributes()
            : $this->totalsItemExtensionFactory->create();

        $qtyIncrements = $this->stockRegistryInterface
            ->getStockItem($quoteItem->getProductId())
            ->getQtyIncrements();
        if ($qtyIncrements) {
            $extensionAttributes->setAwItemQtyIncrements($qtyIncrements);
            $resultTotalsItem->setExtensionAttributes($extensionAttributes);
        }

        return $resultTotalsItem;
    }
}
