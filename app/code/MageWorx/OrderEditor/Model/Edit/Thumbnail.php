<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrderEditor\Model\Edit;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Catalog\Model\Product as ProductModel;

/**
 * Class Thumbnail
 */
class Thumbnail
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * Thumbnail constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param ProductInterfaceFactory $productInterfaceFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterfaceFactory,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->productResource = $productResource;
        $this->productFactory  = $productInterfaceFactory;
        $this->imageHelper     = $imageHelper;
    }

    /**
     * @param OrderItemInterface $item
     * @return \Magento\Catalog\Helper\Image|bool
     */
    public function getImgByItem(OrderItemInterface $item)
    {
        try {
            /** @var ProductInterface|ProductModel $product */
            $product = $this->productFactory->create();
            $product->setStoreId($item->getStoreId());
            $product->setSku($item->getSku());

            $product->setData(
                'thumbnail',
                $this->productResource->getAttributeRawValue($item->getProductId(), 'thumbnail', $item->getStoreId())
            );

            if (!$product->getThumbnail() || $product->getThumbnail() == 'no_selection') {
                return false;
            }

            return $this->imageHelper->init($product, 'product_listing_thumbnail');
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}