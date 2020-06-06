<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Related\Condition\Product;

use Aheadworks\Autorelated\Model\Rule\Related\Condition\Product\Attributes as RelatedAttributes;
use Aheadworks\Autorelated\Model\Rule\Related\Condition\Product\Special\Price as SpecialPrice;
use Magento\Rule\Model\Condition\Context as ConditionContext;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magento\Eav\Model\Config as EavModelConfig;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection as AttrSetCollection;
use Magento\Framework\Locale\FormatInterface;
use Magento\Rule\Block\Editable;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Store\Model\StoreManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Special
 *
 * @package Aheadworks\Autorelated\Model\Rule\Related\Condition\Product
 */
class Special extends RelatedAttributes
{
    /**
     * @param ConditionContext $context
     * @param BackendHelperData $backendData
     * @param EavModelConfig $config
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductResourceModel $productResource
     * @param AttrSetCollection $attrSetCollection
     * @param FormatInterface $localeFormat
     * @param Editable $editable
     * @param ProductType $type
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManager $storeManager
     * @param MetadataPool $metadataPool
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        ConditionContext $context,
        BackendHelperData $backendData,
        EavModelConfig $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ProductResourceModel $productResource,
        AttrSetCollection $attrSetCollection,
        FormatInterface $localeFormat,
        Editable $editable,
        ProductType $type,
        CollectionFactory $productCollectionFactory,
        StoreManager $storeManager,
        MetadataPool $metadataPool,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $editable,
            $type,
            $productCollectionFactory,
            $storeManager,
            $metadataPool,
            $scopeConfig,
            $data
        );
        $this->setType(Special::class);
        $this->setValue(null);
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            [
                'value' => SpecialPrice::class,
                'label' => __('Price (percent value)'),
            ],
        ];

        return ['value' => $conditions, 'label' => __('Product Special')];
    }
}
