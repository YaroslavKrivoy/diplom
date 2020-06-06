<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule\Related\Condition\Product;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom;
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
use Magento\Rule\Model\Condition\Product\AbstractProduct as AbstractProductRule;

/**
 * Class Attributes
 *
 * @package Aheadworks\Autorelated\Model\Rule\Related\Condition\Product
 */
class Attributes extends AbstractProductRule
{
    /**
     * Overvrite condition type for multiselect conditions
     *
     * @var array
     */
    private $multiselectOverwrite = [
        'eq' => 'mEq',
        'neq' => 'mNeq',
        'in' => 'finset',
        'nin' => 'nfinset',
    ];

    /**
     * Overvrite condition type for category conditions
     *
     * @var array
     */
    private $categoryOverwrite = [
        'eq' => 'in',
        'neq' => 'nin',
        'in' => 'in',
        'nin' => 'nin',
    ];

    /**
     * Value type constants
     */
    const VALUE_TYPE_CONSTANT = 'constant';
    const VALUE_TYPE_SAME_AS = 'same_as';
    const VALUE_TYPE_CHILD_OF = 'child_of';

    /**
     * @var ProductType
     */
    private $type;

    /**
     * @var Editable
     */
    private $editable;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManager
     */
    private $storeManager;

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
        $this->editable = $editable;
        $this->type = $type;
        $this->productCollectionFactory = $productCollectionFactory;
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
        $this->setType(Attributes::class);
        $this->setValue(null);
        $this->setValueType(self::VALUE_TYPE_SAME_AS);
        $this->storeManager = $storeManager;
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
     * Add special action product attributes
     *
     * @param array &$attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['type_id'] = __('Type');
    }

    /**
     * Retrieve value by option
     * Rewrite for Retrieve options by Product Type attribute
     *
     * @param mixed $option
     * @return string
     */
    public function getValueOption($option = null)
    {
        if (!$this->getData('value_option') && $this->getAttribute() == 'type_id') {
            $this->setData('value_option', $this->type->getAllOption());
        }
        return parent::getValueOption($option);
    }

    /**
     * Retrieve select option values
     * Rewrite Rewrite for Retrieve options by Product Type attribute
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options') && $this->getAttribute() == 'type_id') {
            $this->setData('value_select_options', $this->type->getAllOption());
        }
        return parent::getValueSelectOptions();
    }

    /**
     * Retrieve input type
     * Rewrite for define input type for Product Type attribute
     *
     * @return string
     */
    public function getInputType()
    {
        $attributeCode = $this->getAttribute();
        if ($attributeCode == 'type_id') {
            return 'select';
        }
        return parent::getInputType();
    }

    /**
     * Retrieve value element type
     * Rewrite for define value element type for Product Type attribute
     *
     * @return string
     */
    public function getValueElementType()
    {
        $attributeCode = $this->getAttribute();
        if ($attributeCode == 'type_id') {
            return 'select';
        }
        return parent::getValueElementType();
    }

    /**
     * Retrieve model content as HTML
     * Rewrite for add value type chooser
     *
     * @return \Magento\Framework\Phrase
     */
    public function asHtml()
    {
        return __(
            'Product %1%2%3%4%5%6%7',
            $this->getTypeElementHtml(),
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getValueTypeElementHtml(),
            $this->getValueElementHtml(),
            $this->getRemoveLinkHtml(),
            $this->getChooserContainerHtml()
        );
    }

    /**
     * Returns options for value type select box
     *
     * @return array
     */
    public function getValueTypeOptions()
    {
        $options = [['value' => self::VALUE_TYPE_CONSTANT, 'label' => __('Exact Value')]];

        if ($this->getAttribute() == 'category_ids') {
            $options[] = [
                'value' => self::VALUE_TYPE_SAME_AS,
                'label' => __('Same as Current Product Category'),
            ];
            $options[] = [
                'value' => self::VALUE_TYPE_CHILD_OF,
                'label' => __('Subcategory of Current Product Category'),
            ];
        } else {
            $options[] = [
                'value' => self::VALUE_TYPE_SAME_AS,
                'label' => __('Current Product %1', $this->getAttributeName()),
            ];
        }

        return $options;
    }

    /**
     * Retrieve Value Type display name
     *
     * @return string
     */
    public function getValueTypeName()
    {
        $options = $this->getValueTypeOptions();
        foreach ($options as $option) {
            if ($option['value'] == $this->getValueType()) {
                return $option['label'];
            }
        }
        return '...';
    }

    /**
     * Retrieve Value Type Select Element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getValueTypeElement()
    {
        $elementId = $this->getPrefix() . '__' . $this->getId() . '__value_type';
        $element = $this->getForm()->addField(
            $elementId,
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][value_type]',
                'values' => $this->getValueTypeOptions(),
                'value' => $this->getValueType(),
                'value_name' => $this->getValueTypeName(),
                'class' => 'value-type-chooser',
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
            $this->editable
        );
        return $element;
    }

    /**
     * Retrieve value type element HTML code
     *
     * @return string
     */
    public function getValueTypeElementHtml()
    {
        $element = $this->getValueTypeElement();
        return $element->getHtml();
    }

    /**
     * Add JS observer
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = parent::getValueAfterElementHtml();
        $valueFieldId = $this->getPrefix() . '__' . $this->getId() . '__value';
        $valueTypeFieldId = $this->getPrefix() . '__' . $this->getId() . '__value_type';
        $constantTypeValue = self::VALUE_TYPE_CONSTANT;
        $html .= "<script type='text/javascript'>
            require([\"jquery\", \"jquery/ui\"],
                (function($) {
                    if (typeof($('#{$valueTypeFieldId}').val()) != 'undefined'
                            && $('#{$valueTypeFieldId}').val() != '{$constantTypeValue}'
                    ) {
                        $('#{$valueFieldId}').parent().parent().hide();
                    } else {
                        $('#{$valueFieldId}').parent().parent().show();
                    }
                    $('#{$valueTypeFieldId}').change(function(){
                        if (this.value != '{$constantTypeValue}') {
                            $('#{$valueFieldId}').parent().parent().hide();
                        } else {
                            $('#{$valueFieldId}').parent().parent().show();
                        }
                    });
                }))
        </script>";

        return $html;
    }

    /**
     * Load attribute property from array
     *
     * @param array $array
     * @return $this
     */
    public function loadArray($array)
    {
        parent::loadArray($array);

        if (isset($array['value_type'])) {
            $this->setValueType($array['value_type']);
        }
        return $this;
    }

    /**
     * Prepare attribute value for product id
     *
     * @param int $productId
     * @param array $additionalParams
     * @return $this
     * @throws \Exception
     */
    public function prepareAttrValueForProductId($productId, $additionalParams = [])
    {
        $linkField = 'entity_id';
        $aliasLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        $configPath = Custom::XML_PATH_CATALOG_FRONTEND_FLAT_CATALOG_PRODUCT;
        if (!$this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE)) {
            $linkField = $aliasLinkField;
        }

        $attribute = $this->getAttributeObject();
        $attributeCode = $attribute->getAttributeCode();
        $valueType = $this->getValueType();
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addIdFilter($productId);

        if ($attribute->getAttributeCode() == 'category_ids') {
            if (!$productCollection->getFlag('aw_autorelated_collection_category_joined')) {
                $catProductTable = $this->_productResource->getTable('catalog_category_product');
                $productCollection
                    ->getSelect()
                    ->joinLeft(
                        ['cat_product' => $catProductTable],
                        "e.entity_id = cat_product.product_id",
                        []
                    )
                ;
                $productCollection->setFlag('aw_autorelated_collection_category_joined', true);
            }
            if ($valueType == self::VALUE_TYPE_SAME_AS) {
                $productCollection->getSelect()->columns(['attr_value' => "cat_product.category_id"]);
            } elseif ($valueType == self::VALUE_TYPE_CHILD_OF) {
                $catEntityTable = $this->_productResource->getTable('catalog_category_entity');
                $productCollection
                    ->getSelect()
                    ->joinLeft(
                        ['cat_entity' => $catEntityTable],
                        'cat_product.category_id = SUBSTR(cat_entity.path, (LOCATE(CONCAT("/", '
                        . 'cat_product.category_id, "/"), cat_entity.path) + 1), LENGTH(cat_product.category_id))',
                        null
                    );
                $productCollection->getSelect()->columns(
                    ['attr_value' => "COALESCE(GROUP_CONCAT(cat_entity.entity_id), cat_product.category_id)"]
                );
            }
            $productCollection = $this->addFilterByCategoryIdsIfSet($productCollection, $additionalParams);
        } else {
            if ($attribute->isStatic()) {
                $productCollection->getSelect()->columns(['attr_value' => "e.{$attributeCode}"]);
            } else {
                $table = $attribute->getBackendTable();
                if (!$productCollection->getFlag("aw_autorelated_{$table}_joined")) {
                    $productCollection
                        ->getSelect()
                        ->joinLeft(
                            ['attr_table' => $table],
                            "e.{$linkField} = attr_table.{$aliasLinkField}",
                            null
                        )
                        ->where('attr_table.attribute_id=?', $attribute->getId())
                    ;
                    $productCollection->setFlag("aw_autorelated_{$table}_joined", true);
                }
                if ($attribute->isScopeGlobal()) {
                    $productCollection->getSelect()->where('attr_table.store_id=?', 0);
                } else {
                    $productCollection->getSelect()->where('attr_table.store_id=?', 0);
                }
                $productCollection->getSelect()->columns(['attr_value' => "attr_table.value"]);
            }
        }

        $value = null;
        foreach ($productCollection->getData() as $row) {
            if (isset($row['attr_value'])) {
                $value[] = $row['attr_value'];
            }
        }
        if (!$this->getValue()) {
            $this->setValue($value);
        }
        $this->setProductValue($value);
        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param array $additionalParams
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function addFilterByCategoryIdsIfSet($productCollection, $additionalParams)
    {
        if (!empty($additionalParams) && is_array($additionalParams['category_ids'])) {
            $productCollection->getSelect()->where(
                'cat_product.category_id IN(?)',
                $additionalParams['category_ids']
            );
        }
        return $productCollection;
    }

    /**
     * Collect valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param null $productId
     * @param array $additionalParams
     * @return $this
     * @throws \Exception
     */
    public function collectValidatedAttributes($productCollection, $productId = null, $additionalParams = [])
    {
        $valueType = $this->getValueType();
        if ($valueType == self::VALUE_TYPE_CONSTANT) {
            return $this->prepareAttributes($productCollection);
        }

        if (!$productId) {
            return $this;
        }

        $this->prepareAttrValueForProductId($productId, $additionalParams);
        $attribute = $this->getAttributeObject();

        $productValue = $this->getProductValue();
        $operator = $this->getOperator();
        if (null === $productValue) {
            $productValue = [''];
        }
        if ($attribute->getAttributeCode() == 'category_ids') {
            if ($valueType == self::VALUE_TYPE_CHILD_OF) {
                switch ($operator) {
                    case '==':
                    case '{}':
                    case '()':
                        $this->setOperator('==');
                        break;
                    case '!=':
                    case '!{}':
                    case '!()':
                        $this->setOperator('!=');
                        break;
                }
                $this->setValue(array_shift($productValue));
                return $this->prepareAttributes($productCollection);
            } else {
                $this->setValue(array_shift($productValue));
                return $this->prepareAttributes($productCollection);
            }
        } else {
            $this->setValue(array_shift($productValue));
            return $this->prepareAttributes($productCollection);
        }
        return $this;
    }

    /**
     * Prepare attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     * @throws \Exception
     */
    public function prepareAttributes($productCollection)
    {
        $linkField = 'entity_id';
        $aliasLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        $configPath = Custom::XML_PATH_CATALOG_FRONTEND_FLAT_CATALOG_PRODUCT;
        if (!$this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE)) {
            $linkField = $aliasLinkField;
        }

        $attribute = $this->getAttributeObject();
        $attributeCode = $attribute->getAttributeCode();
        if ($attribute->getAttributeCode() == 'category_ids') {
            if (!$productCollection->getFlag('aw_autorelated_collection_category_joined')) {
                $catProductTable = $this->_productResource->getTable('catalog_category_product');
                $productCollection
                    ->getSelect()
                    ->joinLeft(
                        ['cat_product' => $catProductTable],
                        "e.entity_id = cat_product.product_id",
                        []
                    )
                    ->group("e.entity_id")
                ;
                $condition = $this->prepareSqlCondition("cat_product.category_id", $this->getValue());
                //$productCollection->getSelect()->having($condition);
                $productCollection = $this->addWhereConditionToCollection($productCollection, $condition);
                $productCollection->setFlag('aw_autorelated_collection_category_joined', true);
            }
        } else {
            if ($attribute->isStatic()) {
                $condition = $this->prepareSqlCondition("e.{$attributeCode}", $this->getValue());
                $productCollection = $this->addWhereConditionToCollection($productCollection, $condition);
            } else {
                $table = $attribute->getBackendTable();
                $tableAlias = 'attr_table_' .$attribute->getId();
                if (!$productCollection->getFlag("aw_autorelated_{$tableAlias}_joined")) {
                    $productCollection
                        ->getSelect()
                        ->joinLeft(
                            [$tableAlias => $table],
                            "e.{$linkField} = {$tableAlias}.{$aliasLinkField}",
                            null
                        )
                    ;
                    $productCollection->setFlag("aw_autorelated_{$tableAlias}_joined", true);
                }
                $conditions = [];
                $conditions[] = $this->_productResource->getConnection()->prepareSqlCondition(
                    "{$tableAlias}.attribute_id",
                    ['eq' => $attribute->getId()]
                );
                if ($attribute->isScopeGlobal()) {
                    $conditions[] = $this->_productResource->getConnection()->prepareSqlCondition(
                        "{$tableAlias}.store_id",
                        ['eq' => 0]
                    );
                } else {
                    $conditions[] = $this->_productResource->getConnection()->prepareSqlCondition(
                        "{$tableAlias}.store_id",
                        [['eq' => $this->storeManager->getStore()->getId()], ['eq' => 0]]
                    );
                }
                $conditions[] = $this->prepareSqlCondition("{$tableAlias}.value", $this->getValue());
                $condition = join(' AND ', $conditions);

                $productCollection = $this->addWhereConditionToCollection($productCollection, $condition);
            }
        }
        return $this;
    }

    /**
     * Prepare condition for sql query
     *
     * @param string $field
     * @param string $value
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function prepareSqlCondition($field, $value)
    {
        $method = $this->getMethod();
        $callback = $this->getPrepareValueCallback();
        if ($callback) {
            $value = call_user_func([$this, $callback], $value);
        }

        if ($this->getAttributeObject()->getAttributeCode() == 'category_ids'
            && array_key_exists($method, $this->categoryOverwrite)
        ) {
            $method = $this->categoryOverwrite[$method];
        }

        if ($this->getAttributeObject()->getFrontendInput() == 'multiselect'
            && array_key_exists($method, $this->multiselectOverwrite)
        ) {
            $method = $this->multiselectOverwrite[$method];
        }

        if ($method =='in' || $method =='nin' || !is_array($value)) {
            $condition = $this->_productResource->getConnection()->prepareSqlCondition(
                $field,
                [$method => $value]
            );
            return $condition;
        }
        if ($method == 'mEq' || $method == 'mNeq') {
            $count = 0;
            if (is_array($value)) {
                $count = count($value);
                $value = implode('|', $value);
            }

            if ($count <= 1) {
                $condition = "REGEXP '^{$value}$'";
            } else {
                // Remove from count value first and last position for regexp
                $centerElementCount = $count - 2;
                $centerValue = str_repeat(",*({$value})", $centerElementCount);
                $condition = "REGEXP '^({$value}){$centerValue},*({$value})$'";
            }
            if ($method == 'mNeq') {
                $condition = 'NOT ' . $condition;
            }

            return $field . ' ' . $condition;
        }

        $conditions = [];
        foreach ($value as $item) {
            if ($method == 'nfinset') {
                $conditions[] = "NOT FIND_IN_SET('{$item}', {$field})";
                continue;
            }
            $conditions[] =  $this->_productResource->getConnection()->prepareSqlCondition(
                $field,
                [$method => $item]
            );
        }
        if ($method == 'nlike' || $method == 'nfinset') {
            $condition  = join(' AND ', $conditions);
        } else {
            $condition  = join(' OR ', $conditions);
        }
        return $condition;
    }

    /**
     * Get method for sql condition
     *
     * @return string
     */
    protected function getMethod()
    {
        $oppositeOperators = [
            '<' => '>=',
            '>' => '<=',
            '==' => '!=',
            '<=' => '>',
            '>=' => '<',
            '!=' => '==',
            '{}' => '!{}',
            '!{}' => '{}',
            '()' => '!()',
            '!()' => '()',
            '[]' => '![]',
            '![]' => '[]',
        ];

        $operator = $this->getOperator();
        if (true !== $this->getTrue()) {
            $operator = $oppositeOperators[$operator];
        }

        $methods = [
            '<' => 'lt',
            '>' => 'gt',
            '==' => 'eq',
            '<=' => 'lteq',
            '>=' => 'gteq',
            '!=' => 'neq',
            '{}' => 'like',
            '!{}' => 'nlike',
            '()' => 'in',
            '!()' => 'nin',
            '[]' => 'finset',
            '![]' => 'nfinset',
        ];

        $method = 'eq';
        if (array_key_exists($operator, $methods)) {
            $method = $methods[$operator];
        }
        return $method;
    }

    /**
     * Get callback for prepare values for sql conditions
     *
     * @return null|string
     */
    protected function getPrepareValueCallback()
    {
        $callbacks = [
            '==' => 'prepareValue',
            '<' => 'prepareValue',
            '>' => 'prepareValue',
            '<=' => 'prepareValue',
            '>=' => 'prepareValue',
            '!=' => 'prepareValue',
            '{}' => 'prepareLikeValue',
            '!{}' => 'prepareLikeValue',
            '()' => 'prepareInValue',
            '!()' => 'prepareInValue',
            '[]' => 'prepareFinsetValue',
            '![]' => 'prepareNonFinsetValue',
            'between' => 'prepareBetweenValue'
        ];
        $operator = $this->getOperator();

        $callback = null;
        if (array_key_exists($operator, $callbacks)) {
            $callback = $callbacks[$operator];
        }

        return $callback;
    }

    /**
     * Prepare value for sql conditions
     *
     * @param string $value
     * @return array
     */
    protected function prepareValue($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = array_map('trim', $value);

        return $value;
    }

    /**
     * Prepare Like value for sql conditions
     *
     * @param string $value
     * @return array
     */
    protected function prepareLikeValue($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = array_map('trim', $value);
        foreach ($value as $key => $item) {
            $value[$key] = '%' . $item . '%';
        }
        return $value;
    }

    /**
     * Prepare In value for sql conditions
     *
     * @param string $value
     * @return array
     */
    protected function prepareInValue($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = array_map('trim', $value);
        return $value;
    }

    /**
     * Prepare Finset value for sql conditions
     *
     * @param string $value
     * @return array
     */
    protected function prepareFinsetValue($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = array_map('trim', $value);
        return $value;
    }

    /**
     * Add where conditions to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string $condition
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function addWhereConditionToCollection($collection, $condition)
    {
        if ($this->getAggregator() && $this->getAggregator() === 'all') {
            $collection->getSelect()->where($condition);
        } else {
            $collection->getSelect()->orWhere($condition);
        }
        return $collection;
    }

    /**
     * Return default operator
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['multiselect'] = ['==', '!=', '()', '!()'];
        }
        return $this->_defaultOperatorInputByType;
    }
}
