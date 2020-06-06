<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Api\Data;

/**
 * Autorelated rule interface
 *
 * @api
 */
interface RuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TYPE = 'type';
    const CODE = 'code';
    const TITLE = 'title';
    const TITLE_STORE_VALUES = 'title_store_values';
    const STATUS = 'status';
    const PRIORITY = 'priority';
    const POSITION = 'position';
    const TEMPLATE_ID = 'template_id';
    const GRID_ROW = 'grid_row';
    const LIMIT = 'limit';
    const SORT_TYPE = 'sort_type';
    const IS_DISPLAY_ADDTOCART = 'is_display_addtocart';
    const CATEGORY_IDS = 'category_ids';
    const VIEWED_CONDITION = 'viewed_condition';
    const PRODUCT_CONDITION = 'product_condition';
    const PRODUCT_CONDITION_TYPE = 'product_condition_type';
    const WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_ONE_CATEGORY = 'wvtav_product_condition_is_suggest_only_one_category';
    const WVTAV_PRODUCT_CONDITION_IS_SUGGEST_ONLY_PRICE_HIGHER = 'wvtav_product_condition_is_suggest_only_price_higher';
    const STORE_IDS = 'store_ids';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const VIEW_COUNT = 'view_count';
    const CLICK_COUNT = 'click_count';
    const IS_DISPLAY_OUTOFSTOCK = 'is_display_outofstock';
    const CUSTOMER_SEGMENT_IDS = 'customer_segment_ids';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get type
     *
     * @return int|null
     */
    public function getType();

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get title store values
     *
     * @return \Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterface[]
     */
    public function getTitleStoreValues();

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Get priority
     *
     * @return int|null
     */
    public function getPriority();

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Get template id
     *
     * @return int|null
     */
    public function getTemplateId();

    /**
     * Get grid row
     *
     * @return int|null
     */
    public function getGridRow();

    /**
     * Get limit
     *
     * @return int|null
     */
    public function getLimit();

    /**
     * Get sort type
     *
     * @return int|null
     */
    public function getSortType();

    /**
     * Get is display addtocart
     *
     * @return string|null
     */
    public function getIsDisplayAddtocart();

    /**
     * Get category ids
     *
     * @return string|null
     */
    public function getCategoryIds();

    /**
     * Get viewed condition
     *
     * @return \Aheadworks\Autorelated\Api\Data\ConditionInterface|null
     */
    public function getViewedCondition();

    /**
     * Get product condition
     *
     * @return \Aheadworks\Autorelated\Api\Data\ConditionInterface|null
     */
    public function getProductCondition();

    /**
     * Get product condition type
     *
     * @return int|null
     */
    public function getProductConditionType();

    /**
     * Get is suggest products from one category only for WVTAV product condition type
     *
     * @return string|null
     */
    public function getWvtavProductConditionIsSuggestOnlyOneCategory();

    /**
     * Get is suggest products only with price higher than price of current product for WVTAV product condition type
     *
     * @return string|null
     */
    public function getWvtavProductConditionIsSuggestOnlyPriceHigher();

    /**
     * Get store ids
     *
     * @return string[]|null
     */
    public function getStoreIds();

    /**
     * Get view count
     *
     * @return int
     */
    public function getViewCount();

    /**
     * Get click count
     *
     * @return int
     */
    public function getClickCount();

    /**
     * Get customer group ids
     *
     * @return string[]|null
     */
    public function getCustomerGroupIds();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Autorelated\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Get is display out of stock products
     *
     * @return string|null
     */
    public function getIsDisplayOutofstock();

    /**
     * Get enterprise customer segment ids
     *
     * @return string[]|null
     */
    public function getCustomerSegmentIds();

    /**
     * Set ID
     *
     * @param int $id
     * @return RuleInterface
     */
    public function setId($id);

    /**
     * Set type
     *
     * @param int $type
     * @return RuleInterface
     */
    public function setType($type);

    /**
     * Set code
     *
     * @param string $code
     * @return RuleInterface
     */
    public function setCode($code);

    /**
     * Set title
     *
     * @param string $title
     * @return RuleInterface
     */
    public function setTitle($title);

    /**
     * Get title store values
     *
     * @param \Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterface[]
     * @return RuleInterface
     */
    public function setTitleStoreValues($titleStoreValues);

    /**
     * Set status
     *
     * @param string $status
     * @return RuleInterface
     */
    public function setStatus($status);

    /**
     * Set priority
     *
     * @param int $priority
     * @return RuleInterface
     */
    public function setPriority($priority);

    /**
     * Set position
     *
     * @param int $position
     * @return RuleInterface
     */
    public function setPosition($position);

    /**
     * Set template id
     *
     * @param int $templateId
     * @return RuleInterface
     */
    public function setTemplateId($templateId);

    /**
     * Set grid row
     *
     * @param int $gridRow
     * @return RuleInterface
     */
    public function setGridRow($gridRow);

    /**
     * Set limit
     *
     * @param int $limit
     * @return RuleInterface
     */
    public function setLimit($limit);

    /**
     * Set sort type
     *
     * @param int $sortType
     * @return RuleInterface
     */
    public function setSortType($sortType);

    /**
     * Set is display addtocart
     *
     * @param string $isDisplayAddtocart
     * @return RuleInterface
     */
    public function setIsDisplayAddtocart($isDisplayAddtocart);

    /**
     * Set category ids
     *
     * @param string $categoryIds
     * @return RuleInterface
     */
    public function setCategoryIds($categoryIds);

    /**
     * Set viewed condition
     *
     * @param \Aheadworks\Autorelated\Api\Data\ConditionInterface $viewedCondition
     * @return RuleInterface
     */
    public function setViewedCondition($viewedCondition);

    /**
     * Set product condition
     *
     * @param \Aheadworks\Autorelated\Api\Data\ConditionInterface $productCondition
     * @return RuleInterface
     */
    public function setProductCondition($productCondition);

    /**
     * Set product condition type
     *
     * @param int $productConditionType
     * @return RuleInterface
     */
    public function setProductConditionType($productConditionType);

    /**
     * Set is suggest products from one category only for WVTAV product condition type
     *
     * @param string $isSuggestOnlyOneCategory
     * @return RuleInterface
     */
    public function setWvtavProductConditionIsSuggestOnlyOneCategory($isSuggestOnlyOneCategory);

    /**
     * Set is suggest products only with price higher than price of current product for WVTAV product condition type
     *
     * @param string $isSuggestOnlyPriceHigher
     * @return RuleInterface
     */
    public function setWvtavProductConditionIsSuggestOnlyPriceHigher($isSuggestOnlyPriceHigher);

    /**
     * Set store ids
     *
     * @param string[] $storeIds
     * @return RuleInterface
     */
    public function setStoreIds($storeIds);

    /**
     * Set customer group ids
     *
     * @param string[] $customerGroupIds
     * @return RuleInterface
     */
    public function setCustomerGroupIds($customerGroupIds);

    /**
     * Set view count
     *
     * @param int $viewCount
     * @return RuleInterface
     */
    public function setViewCount($viewCount);

    /**
     * Set click count
     *
     * @param int $clickCount
     * @return RuleInterface
     */
    public function setClickCount($clickCount);

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Autorelated\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Autorelated\Api\Data\RuleExtensionInterface $extensionAttributes
    );

    /**
     * Set is display out of stock products
     *
     * @param string $isDisplayOutofstock
     * @return RuleInterface
     */
    public function setIsDisplayOutofstock($isDisplayOutofstock);

    /**
     * Set enterprise customer segment ids
     *
     * @param string[] $customerSegmentIds
     * @return RuleInterface
     */
    public function setCustomerSegmentIds($customerSegmentIds);
}
