<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Related\Condition\Product\Special;

use Aheadworks\Autorelated\Model\Rule\Related\Condition\Product\Special as ProductSpecial;
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
use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Price
 *
 * @package Aheadworks\Autorelated\Model\Rule\Related\Condition\Product\Special
 */
class Price extends ProductSpecial
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ScopeConfigInterface
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
        $this->setType(Price::class);
        $this->setAttribute('price');
        $this->setValue(100);
        $this->metadataPool = $metadataPool;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve operator select options array
     *
     * @return array
     */
    private function getOperatorOptionArray()
    {
        return [
            '==' => __('equal to'),
            '>' => __('more'),
            '>=' => __('equals or greater than'),
            '<' => __('less'),
            '<=' => __('equals or less than')
        ];
    }

    /**
     * Set operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption($this->getOperatorOptionArray());
        return $this;
    }

    /**
     * Retrieve rule as HTML formated string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml() . __(
            'Product Price is %1 %2% of Current Product Price',
            $this->getOperatorElementHtml(),
            $this->getValueElementHtml()
        ) . $this->getRemoveLinkHtml();
    }

    /**
     * Collect valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param int|null $productId
     * @param array $additionalParams
     * @return $this
     * @throws \Exception
     */
    public function collectValidatedAttributes($productCollection, $productId = null, $additionalParams = [])
    {
        if (!$productId) {
            return $this;
        }
        $this->setAttribute('price');
        $this->prepareAttrValueForProductId($productId, $additionalParams);

        $method = $this->getMethod();
        $productCollection->getSelect()->group("e.entity_id");
        $productCollection->addPriceData();
        $productCollection->load();
        $productValue = $this->getProductValue();
        if (null === $productValue) {
            $productValue = [''];
        }
        $value = array_shift($productValue);
        $condition = $this->_productResource->getConnection()->prepareSqlCondition(
            "price_index.price",
            [$method => $value / 100 * $this->getValue()]
        );
        $productCollection = $this->addWhereConditionToCollection($productCollection, $condition);

        return $this;
    }

    /**
     * Validate product whether it meets conditions
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        $isValid = false;
        $productValue = $this->getProductValue();
        if (null === $productValue) {
            $productValue = [''];
        }
        $value = array_shift($productValue);
        $resultValue = $value / 100 * $this->getValue();

        if ($price = $model->getData(ProductInterface::PRICE)) {
            $this->setValue($resultValue);
            $isValid = $this->validateAttribute($price);
        }

        return $isValid;
    }
}
