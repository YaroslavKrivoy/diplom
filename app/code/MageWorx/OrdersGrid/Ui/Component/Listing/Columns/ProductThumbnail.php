<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersGrid\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductThumbnail extends Column
{
    const NAME = 'product_thumbnail';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->productRepository = $productRepository;
        $this->searchCriteria = $criteria;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!$dataSource) {
            return $dataSource;
        }

        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $productIds = [];
        foreach ($dataSource['data']['items'] as & $item) {
            if (empty($item[$fieldName]) || !$item[$fieldName]) {
                continue;
            }
            $productIds = array_merge($productIds, explode(',', $item[$fieldName]));
        }

        if (empty($productIds)) {
            return $dataSource;
        }

        $productIds = array_unique($productIds);
        /** @var \Magento\Framework\Api\Search\FilterGroup $idsFilter */
        $idsFilter = $this->filterGroupBuilder->create();
        $idsFilter->setFilters(
            [
                $this->filterBuilder
                    ->setField('entity_id')
                    ->setConditionType('in')
                    ->setValue($productIds)
                    ->create(),
            ]
        );
        $this->searchCriteria->setFilterGroups([$idsFilter]);
        $products = $this->productRepository->getList($this->searchCriteria);
        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $productItems */
        $productItems = $products->getItems();

        if (empty($productItems)) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as & $item) {
            if (!$item[$fieldName]) {
                continue;
            }
            $productIdsCurrent = explode(',', $item[$fieldName]);
            foreach ($productIdsCurrent as $currentProductId) {
                if (empty($productItems[$currentProductId])) {
                    continue;
                }
                /** @var \Magento\Catalog\Model\Product $productItems[$currentProductId] */
                $imageHelper = $this->imageHelper->init($productItems[$currentProductId], 'product_listing_thumbnail');
                $url = $imageHelper->getUrl();
                $item['product_thumbnails'][$currentProductId]['src'] = $url;
                $item['product_thumbnails'][$currentProductId]['alt'] = $imageHelper->getLabel();
                $item['product_thumbnails'][$currentProductId]['pid'] = $currentProductId;
            }
        }

        return $dataSource;
    }
}
