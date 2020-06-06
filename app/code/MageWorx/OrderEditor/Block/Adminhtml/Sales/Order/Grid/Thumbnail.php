<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Grid;

/**
 * Class Thumbnail
 */
class Thumbnail extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @var \MageWorx\OrderEditor\Model\Edit\Thumbnail
     */
    private $thumbnailModel;

    /**
     * Thumbnail constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \MageWorx\OrderEditor\Model\Edit\Thumbnail $thumbnailModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \MageWorx\OrderEditor\Model\Edit\Thumbnail $thumbnailModel,
        array $data = []
    ) {
        $this->thumbnailModel = $thumbnailModel;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }


    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return \Magento\Catalog\Helper\Image|null
     */
    public function getImageHelper($item)
    {
        return $this->thumbnailModel->getImgByItem($item);
    }
}
