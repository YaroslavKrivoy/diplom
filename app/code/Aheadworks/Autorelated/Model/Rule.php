<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\ResourceModel\Rule as ResourceRule;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Autorelated\Model\ResourceModel\Validator\CodeIsUnique as CodeIsUniqueValidator;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Aheadworks\Autorelated\Model\Rule\Related\ProductFactory as RelatedProductFactory;
use Aheadworks\Autorelated\Model\Rule\Related\CategoryProductFactory as RelatedCategoryProductFactory;
use Aheadworks\Autorelated\Model\Rule\Viewed\ProductFactory as ViewedProductFactory;
use Aheadworks\Autorelated\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Autorelated\Model\Rule\TypeResolver;
use Aheadworks\Autorelated\Model\Source\Type;
use Magento\Framework\DataObject\IdentityInterface;
use Aheadworks\Autorelated\Model\Serialize\SerializeInterface;
use Aheadworks\Autorelated\Model\Serialize\Factory as SerializeFactory;

/**
 * Class Rule
 *
 * @package Aheadworks\Autorelated\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Rule extends AbstractModel implements RuleInterface, IdentityInterface
{
    /**
     * Rule cache tag
     */
    const CACHE_TAG = 'aw_arp_rule';

    /**
     * Rule list cache tag
     */
    const CACHE_LIST_TAG = 'aw_arp_rule_list';

    /**
     * @var CodeIsUniqueValidator
     */
    private $codeIsUniqueValidator;

    /**
     * @var RelatedProductFactory
     */
    private $relatedProductFactory;

    /**
     * @var RelatedCategoryProductFactory
     */
    private $relatedCategoryProductFactory;

    /**
     * @var ViewedProductFactory
     */
    private $viewedProductFactory;

    /**
     * @var \Aheadworks\Autorelated\Model\Rule\Related\Product
     */
    private $relatedProductRule;

    /**
     * @var \Aheadworks\Autorelated\Model\Rule\Viewed\Product
     */
    private $viewedProductRule;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var TypeResolver
     */
    private $ruleTypeResolver;

    /**
     * @var SerializeInterface
     */
    private $serializer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CodeIsUniqueValidator $codeIsUniqueValidator
     * @param RelatedProductFactory $relatedProductFactory
     * @param RelatedCategoryProductFactory $relatedCategoryProductFactory
     * @param ViewedProductFactory $viewedProductFactory
     * @param ConditionConverter $conditionConverter
     * @param TypeResolver $ruleTypeResolver
     * @param SerializeFactory $serializeFactory
     * @param ResourceRule|null $resource
     * @param ResourceRule\Collection|null $resourceCollection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CodeIsUniqueValidator $codeIsUniqueValidator,
        RelatedProductFactory $relatedProductFactory,
        RelatedCategoryProductFactory $relatedCategoryProductFactory,
        ViewedProductFactory $viewedProductFactory,
        ConditionConverter $conditionConverter,
        TypeResolver $ruleTypeResolver,
        SerializeFactory $serializeFactory,
        ResourceRule $resource = null,
        ResourceRule\Collection $resourceCollection = null
    ) {
        $this->relatedProductFactory = $relatedProductFactory;
        $this->relatedCategoryProductFactory = $relatedCategoryProductFactory;
        $this->viewedProductFactory = $viewedProductFactory;
        $this->codeIsUniqueValidator = $codeIsUniqueValidator;
        $this->conditionConverter = $conditionConverter;
        $this->ruleTypeResolver = $ruleTypeResolver;
        $this->serializer = $serializeFactory->create();
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceRule::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleStoreValues()
    {
        return $this->getData(self::TITLE_STORE_VALUES);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getGridRow()
    {
        return $this->getData(self::GRID_ROW);
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->getData(self::LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortType()
    {
        return $this->getData(self::SORT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisplayAddtocart()
    {
        return $this->getData(self::IS_DISPLAY_ADDTOCART);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->getData(self::CATEGORY_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewedCondition()
    {
        return $this->getData(self::VIEWED_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCondition()
    {
        return $this->getData(self::PRODUCT_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductConditionType()
    {
        return $this->getData(self::PRODUCT_CONDITION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getWvtavProductConditionIsSuggestOnlyOneCategory()
    {
        return $this->getData(self::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_ONE_CATEGORY);
    }

    /**
     * {@inheritdoc}
     */
    public function getWvtavProductConditionIsSuggestOnlyPriceHigher()
    {
        return $this->getData(self::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_PRICE_HIGHER);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupIds()
    {
        return $this->getData(self::CUSTOMER_GROUP_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewCount()
    {
        return $this->getData(self::VIEW_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getClickCount()
    {
        return $this->getData(self::CLICK_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisplayOutofstock()
    {
        return $this->getData(self::IS_DISPLAY_OUTOFSTOCK);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerSegmentIds()
    {
        return $this->getData(self::CUSTOMER_SEGMENT_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitleStoreValues($titleStoreValues)
    {
        return $this->setData(self::TITLE_STORE_VALUES, $titleStoreValues);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateId($templateId)
    {
        return $this->setData(self::TEMPLATE_ID, $templateId);
    }

    /**
     * {@inheritdoc}
     */
    public function setGridRow($gridRow)
    {
        return $this->setData(self::GRID_ROW, $gridRow);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit)
    {
        return $this->setData(self::LIMIT, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortType($sortType)
    {
        return $this->setData(self::SORT_TYPE, $sortType);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDisplayAddtocart($isDisplayAddtocart)
    {
        return $this->setData(self::IS_DISPLAY_ADDTOCART, $isDisplayAddtocart);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds($categoryIds)
    {
        return $this->setData(self::CATEGORY_IDS, $categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewedCondition($viewedCondition)
    {
        return $this->setData(self::VIEWED_CONDITION, $viewedCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductCondition($productCondition)
    {
        return $this->setData(self::PRODUCT_CONDITION, $productCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductConditionType($productConditionType)
    {
        return $this->setData(self::PRODUCT_CONDITION_TYPE, $productConditionType);
    }

    /**
     * {@inheritdoc}
     */
    public function setWvtavProductConditionIsSuggestOnlyOneCategory($isSuggestOnlyOneCategory)
    {
        return $this->setData(self::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_ONE_CATEGORY, $isSuggestOnlyOneCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function setWvtavProductConditionIsSuggestOnlyPriceHigher($isSuggestOnlyPriceHigher)
    {
        return $this->setData(self::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_PRICE_HIGHER, $isSuggestOnlyPriceHigher);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        return $this->setData(self::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewCount($viewCount)
    {
        return $this->setData(self::VIEW_COUNT, $viewCount);
    }

    /**
     * {@inheritdoc}
     */
    public function setClickCount($clickCount)
    {
        return $this->setData(self::CLICK_COUNT, $clickCount);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\Aheadworks\Autorelated\Api\Data\RuleExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDisplayOutofstock($isDisplayOutofstock)
    {
        return $this->setData(self::IS_DISPLAY_OUTOFSTOCK, $isDisplayOutofstock);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerSegmentIds($customerSegmentIds)
    {
        return $this->setData(self::CUSTOMER_SEGMENT_IDS, $customerSegmentIds);
    }

    /**
     * Return related product model with load conditions
     *
     * @return \Aheadworks\Autorelated\Model\Rule\Related\Product
     */
    public function getRelatedProductRule()
    {
        if (null === $this->relatedProductRule) {
            $conditionArray = $this->conditionConverter->dataModelToArray($this->getProductCondition());
            if ($this->ruleTypeResolver->isRuleTypeUseCategoryRelatedProductCondition($this->getType())) {
                $this->relatedProductRule = $this->relatedCategoryProductFactory->create();
            } else {
                $this->relatedProductRule = $this->relatedProductFactory->create();
            }
            $this->relatedProductRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionArray);
        }

        return $this->relatedProductRule;
    }

    /**
     * Return viewed product model with load conditions
     *
     * @return \Aheadworks\Autorelated\Model\Rule\Viewed\Product
     */
    public function getViewedProductRule()
    {
        if (null === $this->viewedProductRule) {
            $conditionArray = $this->conditionConverter->dataModelToArray($this->getViewedCondition());
            $this->viewedProductRule = $this->viewedProductFactory->create();
            $this->viewedProductRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionArray);
        }

        return $this->viewedProductRule;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if (is_array($this->getViewedCondition())) {
            $this->setViewedCondition($this->serializer->serialize($this->getViewedCondition()));
        }
        if (is_array($this->getProductCondition())) {
            $this->setProductCondition($this->serializer->serialize($this->getProductCondition()));
        }

        $type = Type::PRODUCT_BLOCK_TYPE;
        if ($this->getPosition()) {
            $type = $this->ruleTypeResolver->getType($this->getPosition());
        }
        $this->setType($type);

        $this->validateBeforeSave();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validateBeforeSave()
    {
        if (!$this->codeIsUniqueValidator->validate($this)) {
            throw new \Magento\Framework\Exception\AlreadyExistsException(
                __('Rule name should be unique')
            );
        }

        $titleStoreIds = [];
        if ($this->getTitleStoreValues() && (is_array($this->getTitleStoreValues()))) {
            foreach ($this->getTitleStoreValues() as $titleStoreValue) {
                if (!in_array($titleStoreValue['store_id'], $titleStoreIds)) {
                    array_push($titleStoreIds, $titleStoreValue['store_id']);
                } else {
                    throw new \Magento\Framework\Validator\Exception(
                        __('Store view should be unique')
                    );
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [
            self::CACHE_LIST_TAG,
            self::CACHE_TAG . '_' . $this->getId()
        ];
    }
}
