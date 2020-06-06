<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Autorelated\Model\Rule\Related\Validator as RelatedValidator;
use Aheadworks\Autorelated\Model\Rule\Viewed\Validator as ViewedValidator;
use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterfaceFactory;
use Aheadworks\Autorelated\Api\BlockRepositoryInterface;
use Aheadworks\Autorelated\Api\Data\BlockInterfaceFactory;
use Aheadworks\Autorelated\Api\Data\BlockInterface;
use Aheadworks\Autorelated\Api\Data\BlockSearchResultsInterface;
use Aheadworks\Autorelated\Model\Source\Status;
use Aheadworks\Autorelated\Model\Config;
use \Magento\Framework\App\Http\Context as HttpContext;
use \Magento\Customer\Model\Context as CustomerContext;

/**
 * Class BlockRepository
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BlockRepository implements BlockRepositoryInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var RelatedValidator
     */
    private $relatedValidator;

    /**
     * @var ViewedValidator
     */
    private $viewedValidator;

    /**
     * @var BlockSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var BlockInterfaceFactory
     */
    private $blockFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param RuleRepositoryInterface $ruleRepository
     * @param StoreManagerInterface $storeManager
     * @param RelatedValidator $relatedValidator
     * @param ViewedValidator $viewedValidator
     * @param BlockSearchResultsInterfaceFactory $searchResultsFactory
     * @param BlockInterfaceFactory $blockFactory
     * @param Config $config
     * @param HttpContext $httpContext
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        RuleRepositoryInterface $ruleRepository,
        StoreManagerInterface $storeManager,
        RelatedValidator $relatedValidator,
        ViewedValidator $viewedValidator,
        BlockSearchResultsInterfaceFactory $searchResultsFactory,
        BlockInterfaceFactory $blockFactory,
        Config $config,
        HttpContext $httpContext
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->ruleRepository = $ruleRepository;
        $this->storeManager = $storeManager;
        $this->relatedValidator = $relatedValidator;
        $this->viewedValidator = $viewedValidator;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->blockFactory = $blockFactory;
        $this->config = $config;
        $this->httpContext = $httpContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $blockType,
        $blockPosition = null,
        $allBlocks = false,
        $storeId = null,
        $ruleIds = []
    ) {
        $actualRuleList = $this->getActualRuleList($blockType, $blockPosition, $storeId, $ruleIds);
        $blockItems = [];
        foreach ($actualRuleList as $rule) {
            if ($this->viewedValidator->canShow($rule, $blockType)) {
                $products = $this->relatedValidator->validateAndGetProducts($rule, $blockType);
                if (count($products)) {
                    /**
                     * @var BlockInterface $blockDataModel
                     */
                    $blockDataModel = $this->blockFactory->create();
                    $blockDataModel->setRule($rule);
                    $blockDataModel->setProducts($products);
                    $blockItems[] = $blockDataModel;

                    // Break of the loop if we want to display only one block
                    if (!$allBlocks) {
                        break;
                    }
                }
            }
        }

        /**
         * @var BlockSearchResultsInterface $blockSearchResults
         */
        $blockSearchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($this->searchCriteriaBuilder->create());
        $blockSearchResults->setItems($blockItems);
        $blockSearchResults->setTotalCount(count($blockItems));

        return $blockSearchResults;
    }

    /**
     * Retrieve list of actual rules for current block position
     *
     * @param int $blockType
     * @param int $blockPosition
     * @param int|null $storeId
     * @param string[] $ruleIds
     * @return RuleInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getActualRuleList($blockType, $blockPosition, $storeId, $ruleIds)
    {
        $ruleSortOrder = $this->getDefaultSortOrderForRuleList();
        $ruleSearchCriteria = $this->getPreparedSearchCriteriaForRuleList(
            $blockType,
            $blockPosition,
            $ruleIds,
            $ruleSortOrder
        );

        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $actualRuleList = $this->ruleRepository
            ->getList($ruleSearchCriteria, $storeId)
            ->getItems();
        return $actualRuleList;
    }

    /**
     * Retrieve default sort order for rule list
     *
     * @return SortOrder
     */
    private function getDefaultSortOrderForRuleList()
    {
        return $this->sortOrderBuilder
            ->setField(RuleInterface::PRIORITY)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();
    }

    /**
     * Retrieve search criteria for rule list
     *
     * @param int $blockType
     * @param int $blockPosition
     * @param string[] $ruleIds
     * @param SortOrder $sortOrder
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function getPreparedSearchCriteriaForRuleList($blockType, $blockPosition, $ruleIds, $sortOrder)
    {
        if ($this->isNeedToAddBlockPositionFilter($blockPosition)) {
            $this->searchCriteriaBuilder->addFilter(RuleInterface::POSITION, $blockPosition);
        }
        $this->searchCriteriaBuilder
            ->addFilter(RuleInterface::STATUS, Status::STATUS_ENABLED)
            ->addFilter(RuleInterface::TYPE, $blockType)
            ->addFilter(RuleInterface::CUSTOMER_GROUP_IDS, $this->getCurrentCustomerGroupId())
            ->addFilter(RuleInterface::STORE_IDS, $this->getCurrentStoreId())
            ->addSortOrder($sortOrder);

        if ($this->isNeedToAddCustomerSegmentFilter()) {
            $this->searchCriteriaBuilder
                ->addFilter(RuleInterface::CUSTOMER_SEGMENT_IDS, $this->getActiveSegmentIdsFromCurrentCustomer())
            ;
        }

        if ($this->isNeedToAddRuleIdFilter($ruleIds)) {
            $this->searchCriteriaBuilder->addFilter(RuleInterface::ID, $ruleIds);
        }

        return $this->searchCriteriaBuilder->create();
    }

    /**
     * Check if need to add block position filter
     *
     * @param int $blockPosition
     * @return bool
     */
    private function isNeedToAddBlockPositionFilter($blockPosition)
    {
        return ($blockPosition !== null);
    }

    /**
     * Check if need to add customer segment filter
     *
     * @return bool
     */
    private function isNeedToAddCustomerSegmentFilter()
    {
        return ($this->config->isEnterpriseCustomerSegmentEnabled());
    }

    /**
     * Check if need to add  rule id filter
     *
     * @param string[] $ruleIds
     * @return bool
     */
    private function isNeedToAddRuleIdFilter($ruleIds)
    {
        return (!empty($ruleIds));
    }

    /**
     * Retrieve group id for current customer
     *
     * @return mixed|null
     */
    private function getCurrentCustomerGroupId()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    private function getCurrentStoreId()
    {
        $storeCode = $this->getCurrentStoreCode();
        $currentStore = $this->storeManager->getStore($storeCode);
        return $currentStore->getId();
    }

    /**
     * Retrieve current store code
     *
     * @return mixed|null
     */
    private function getCurrentStoreCode()
    {
        return $this->httpContext->getValue(StoreManagerInterface::CONTEXT_STORE);
    }

    /**
     * Get active enterprise segment ids from current customer
     *
     * @return array
     */
    private function getActiveSegmentIdsFromCurrentCustomer()
    {
        $activeSegmentIds = [];
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            if ($this->config->isEnterpriseCustomerSegmentEnabled()) {
                $segmentIds = $this->getCurrentSegmentIds();
                $activeSegmentIds = $this->getActiveCustomerSegments($segmentIds);
            }
        }
        return $activeSegmentIds;
    }

    /**
     * Retrieve segment ids for current customer
     *
     * @return array|mixed|null
     */
    private function getCurrentSegmentIds()
    {
        $segmentIds = [];
        $enterpriseSegmentsHelper = $this->config->getEnterpriseCustomerSegmentHelper();
        if (is_object($enterpriseSegmentsHelper)) {
            $segmentIds = $this->httpContext->getValue($enterpriseSegmentsHelper::CONTEXT_SEGMENT);
        }
        return $segmentIds;
    }

    /**
     * Get active enterprise customer segment ids
     *
     * @param array $segmentIds
     * @return array
     */
    private function getActiveCustomerSegments($segmentIds)
    {
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            $customerSegmentResourceModel = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('\Magento\CustomerSegment\Model\ResourceModel\Segment')
            ;
            if (is_object($customerSegmentResourceModel)) {
                $segmentIds = $customerSegmentResourceModel->getActiveSegmentsByIds($segmentIds);
            }
        }
        return $segmentIds;
    }
}
