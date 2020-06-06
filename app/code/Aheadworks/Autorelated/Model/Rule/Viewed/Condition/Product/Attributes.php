<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Product;

use Magento\Rule\Model\Condition\Context as ConditionContext;
use Magento\Backend\Helper\Data as BackendHelperData;
use Magento\Eav\Model\Config as EavModelConfig;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection as AttrSetCollection;
use Magento\Framework\Locale\FormatInterface;
use Magento\Store\Model\StoreManager;
use Magento\CatalogRule\Model\Rule\Condition\Product as CatalogRuleProduct;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Attributes
 *
 * @package Aheadworks\Autorelated\Model\Rule\Viewed\Condition\Product
 */
class Attributes extends CatalogRuleProduct
{
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ConditionContext $context
     * @param BackendHelperData $backendData
     * @param EavModelConfig $config
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductResourceModel $productResource
     * @param AttrSetCollection $attrSetCollection
     * @param FormatInterface $localeFormat
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
            $data
        );
        $this->storeManager = $storeManager;
        $this->setType(Attributes::class);
        $this->setValue(null);
        $this->metadataPool = $metadataPool;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = [];
        foreach ($attributes as $code => $label) {
            $conditions[] = ['value' => $this->getType() . '|' . $code, 'label' => $label];
        }

        return ['value' => $conditions, 'label' => __('Product Attributes')];
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                } elseif ($this->getRule()->getJsFormObject()) {
                    $url .= '/form/' . $this->getRule()->getJsFormObject();
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }
}
