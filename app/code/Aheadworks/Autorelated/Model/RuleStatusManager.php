<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model;

use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Source\Status;
use Aheadworks\Autorelated\Model\Source\ProductConditionType;
use Aheadworks\Autorelated\Api\Data\RuleSearchResultsInterface;

/**
 * Class RuleStatusManager
 * @package Aheadworks\Autorelated\Model
 */
class RuleStatusManager
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param RuleRepositoryInterface $ruleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    /**
     * Check if rule status changing impossible due to disabled WVTAV functionality
     *
     * @param int $ruleId
     * @return bool
     */
    public function isRuleStatusLockedByWvtavFunctionality($ruleId)
    {
        $rule = $this->ruleRepository->get($ruleId);
        return ((!$this->config->isWvtavFunctionalityEnabled())
            && ($rule->getProductConditionType() == ProductConditionType::WHO_VIEWED_THIS_ALSO_VIEWED)
            && ($rule->getStatus() == Status::STATUS_DISABLED)
        );
    }

    /**
     * Switch status for the rule with specified id
     *
     * @param int $ruleId
     */
    public function switchRuleStatus($ruleId)
    {
        /** @var RuleInterface $rule */
        $rule = $this->ruleRepository->get($ruleId);
        $rule->setStatus($this->getOppositeStatus($rule->getStatus()));
        $this->ruleRepository->save($rule);
    }

    /**
     * Retrieve opposite status for the current one
     *
     * @param int $currentStatus
     * @return int
     */
    private function getOppositeStatus($currentStatus)
    {
        $oppositeStatus = Status::STATUS_DISABLED;
        switch ($currentStatus) {
            case Status::STATUS_ENABLED:
                $oppositeStatus = Status::STATUS_DISABLED;
                break;
            case Status::STATUS_DISABLED:
                $oppositeStatus = Status::STATUS_ENABLED;
                break;
        }
        return $oppositeStatus;
    }

    /**
     * Set disabled status to all specified rules
     *
     * @param RuleInterface[] $rulesArray
     */
    public function massDisable($rulesArray)
    {
        foreach ($rulesArray as $ruleItem) {
            $ruleItem->setStatus(Status::STATUS_DISABLED);
            $this->ruleRepository->save($ruleItem);
        }
    }

    /**
     * Disable rules, connected to WVTAV functionality
     */
    public function disableRulesConnectedToWvtavFunctionality()
    {
        $rulesToDisable = $this->getNonDisabledRulesWithWvtavProductConditionType();
        $this->massDisable($rulesToDisable);
    }

    /**
     * Get non-disabled rules with product condition type set to WVTAV
     *
     * @return RuleInterface[]
     */
    private function getNonDisabledRulesWithWvtavProductConditionType()
    {
        /** @var RuleSearchResultsInterface $searchResults */
        $searchResults = $this->ruleRepository->getList(
            $this->getSearchCriteriaForNonDisabledRulesWithWvtavProductConditionType()
        );
        return $searchResults->getItems();
    }

    /**
     * Get search criteria for rules with product condition type set to WVTAV
     *
     * @return SearchCriteria
     */
    private function getSearchCriteriaForNonDisabledRulesWithWvtavProductConditionType()
    {
        $this->searchCriteriaBuilder
            ->addFilter(
                RuleInterface::STATUS,
                Status::STATUS_DISABLED,
                'neq'
            )->addFilter(
                RuleInterface::PRODUCT_CONDITION_TYPE,
                ProductConditionType::WHO_VIEWED_THIS_ALSO_VIEWED
            );
        return $this->searchCriteriaBuilder->create();
    }
}
