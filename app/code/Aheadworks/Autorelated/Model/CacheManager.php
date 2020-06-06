<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Model;

use Magento\Framework\Indexer\CacheContextFactory;
use Magento\Framework\Event\ManagerInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Autorelated\Model\Source\Status;
use Aheadworks\Autorelated\Model\Source\ProductConditionType;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;

/**
 * Class CacheManager
 * @package Aheadworks\Autorelated\Model
 */
class CacheManager
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CacheContextFactory
     */
    private $cacheContextFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param Config $config
     * @param CacheContextFactory $cacheContextFactory
     * @param ManagerInterface $eventManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Config $config,
        CacheContextFactory $cacheContextFactory,
        ManagerInterface $eventManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->config = $config;
        $this->cacheContextFactory = $cacheContextFactory;
        $this->eventManager = $eventManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Flush cache for WVTAV display mode if needed
     *
     * @return $this
     */
    public function flushCacheForWvtavIfNeeded()
    {
        if ($this->isNeedToFlushCacheForWvtav()) {
            $this->flushCacheForWvtav();
        }
        return $this;
    }

    /**
     * Check if need to flush cache for WVTAV display mode
     *
     * @return bool
     */
    private function isNeedToFlushCacheForWvtav()
    {
        $flag = false;
        if ($this->config->isWvtavFunctionalityEnabled()) {
            $rulesCount = $this->getTotalCountOfWvtavRulesToFlushCacheFor();
            $flag = ($rulesCount > 0);
        }
        return $flag;
    }

    /**
     * Retrieve total count of WVTAV-rules that requires cache flushing
     *
     * @return int
     */
    private function getTotalCountOfWvtavRulesToFlushCacheFor()
    {
        $searchCriteria = $this->getSearchCriteriaForWvtavRulesToFlushCacheFor();
        $totalCount = $this->ruleRepository
            ->getList($searchCriteria)
            ->getTotalCount();
        return $totalCount;
    }

    /**
     * Retrieve search criteria for WVTAV-rules that requires cache flushing
     *
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function getSearchCriteriaForWvtavRulesToFlushCacheFor()
    {
        $this->searchCriteriaBuilder
            ->addFilter(
                RuleInterface::STATUS,
                Status::STATUS_ENABLED
            )->addFilter(
                RuleInterface::PRODUCT_CONDITION_TYPE,
                ProductConditionType::WHO_VIEWED_THIS_ALSO_VIEWED
            );

        return $this->searchCriteriaBuilder->create();
    }

    /**
     * Flush cache for WVTAV display mode
     *
     * @return $this
     */
    private function flushCacheForWvtav()
    {
        $cacheContext = $this->getCacheContextToFlushCacheForWvtav();
        $this->dispatchEventToFlushCache($cacheContext);
        return $this;
    }

    /**
     * Retrieve cache context to flush cache for WVTAV display mode
     *
     * @return \Magento\Framework\Indexer\CacheContext
     */
    private function getCacheContextToFlushCacheForWvtav()
    {
        /** @var \Magento\Framework\Indexer\CacheContext $cacheContext */
        $cacheContext = $this->cacheContextFactory->create();
        $cacheTags = $this->getTagsToFlushCacheForWvtav();
        $cacheContext->registerTags($cacheTags);
        return $cacheContext;
    }

    /**
     * Retrieve array of tags to flush cache for WVTAV display mode
     *
     * @return array
     */
    private function getTagsToFlushCacheForWvtav()
    {
        return [Rule::CACHE_LIST_TAG];
    }

    /**
     * Dispatch event to flush cache for tags specified in cache context
     *
     * @param \Magento\Framework\Indexer\CacheContext $cacheContext
     * @return $this
     */
    private function dispatchEventToFlushCache($cacheContext)
    {
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $cacheContext]);
        return $this;
    }
}
