<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Data;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Rule data model
 *
 * @codeCoverageIgnore
 */
class Rule extends AbstractExtensibleObject implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleStoreValues()
    {
        return $this->_get(self::TITLE_STORE_VALUES);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->_get(self::PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateId()
    {
        return $this->_get(self::TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getGridRow()
    {
        return $this->_get(self::GRID_ROW);
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->_get(self::LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortType()
    {
        return $this->_get(self::SORT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisplayAddtocart()
    {
        return $this->_get(self::IS_DISPLAY_ADDTOCART);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->_get(self::CATEGORY_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewedCondition()
    {
        return $this->_get(self::VIEWED_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCondition()
    {
        return $this->_get(self::PRODUCT_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductConditionType()
    {
        return $this->_get(self::PRODUCT_CONDITION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getWvtavProductConditionIsSuggestOnlyOneCategory()
    {
        return $this->_get(self::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_ONE_CATEGORY);
    }

    /**
     * {@inheritdoc}
     */
    public function getWvtavProductConditionIsSuggestOnlyPriceHigher()
    {
        return $this->_get(self::WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_PRICE_HIGHER);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->_get(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupIds()
    {
        return $this->_get(self::CUSTOMER_GROUP_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewCount()
    {
        return $this->_get(self::VIEW_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getClickCount()
    {
        return $this->_get(self::CLICK_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisplayOutofstock()
    {
        return $this->_get(self::IS_DISPLAY_OUTOFSTOCK);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerSegmentIds()
    {
        return $this->_get(self::CUSTOMER_SEGMENT_IDS);
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
        return $this->_setExtensionAttributes($extensionAttributes);
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
}
