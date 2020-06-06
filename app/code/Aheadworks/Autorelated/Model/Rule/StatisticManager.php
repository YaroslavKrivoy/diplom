<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Model\Rule;

use Aheadworks\Autorelated\Api\StatisticManagerInterface;
use Aheadworks\Autorelated\Api\Data\RuleStatisticInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Autorelated\Model\ResourceModel\RuleStatisticRepository;
use Aheadworks\Autorelated\Model\RuleStatisticFactory;
use Aheadworks\Autorelated\Model\CustomerStatistic\Manager as CustomerStatisticManager;

/**
 * Class StatisticManager
 *
 * @package Aheadworks\Autorelated\Model\Rule
 */
class StatisticManager implements StatisticManagerInterface
{
    /**
     * @var RuleStatisticRepository
     */
    private $ruleStatisticRepository;

    /**
     * @var CustomerStatisticManager
     */
    private $customerStatisticManager;

    /**
     * @var RuleStatisticFactory
     */
    private $ruleStatisticFactory;

    /**
     * @param RuleStatisticRepository $ruleStatisticRepository
     * @param CustomerStatisticManager $customerStatisticManager
     * @param RuleStatisticFactory $ruleStatisticFactory
     */
    public function __construct(
        RuleStatisticRepository $ruleStatisticRepository,
        CustomerStatisticManager $customerStatisticManager,
        RuleStatisticFactory $ruleStatisticFactory
    ) {
        $this->ruleStatisticRepository = $ruleStatisticRepository;
        $this->customerStatisticManager = $customerStatisticManager;
        $this->ruleStatisticFactory = $ruleStatisticFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRuleViews($ruleId)
    {
        if ($this->customerStatisticManager->isNeedToUpdateViewsStatisticForRule($ruleId)) {
            return $this->updateRuleStatisticCounter($ruleId, RuleStatisticInterface::VIEW_COUNT);
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateRuleClicks($ruleId)
    {
        if ($this->customerStatisticManager->isNeedToUpdateClicksStatisticForRule($ruleId)) {
            return $this->updateRuleStatisticCounter($ruleId, RuleStatisticInterface::CLICK_COUNT);
        } else {
            return false;
        }
    }

    /**
     * Update rule statistic counter
     *
     * @param int $ruleId
     * @param string $counterKey
     * @return RuleStatisticInterface
     */
    private function updateRuleStatisticCounter($ruleId, $counterKey)
    {
        $ruleStatisticModel = $this->retrieveRecordToUpdate($ruleId);
        $this->updateRuleCounter($ruleStatisticModel, $counterKey);
        $this->ruleStatisticRepository->save($ruleStatisticModel);
        return $ruleStatisticModel;
    }

    /**
     * Retrieve rule statistic record to update
     *
     * @param int $ruleId
     * @return RuleStatisticInterface
     */
    private function retrieveRecordToUpdate($ruleId)
    {
        try {
            $ruleStatisticModel = $this->ruleStatisticRepository->get($ruleId);
        } catch (NoSuchEntityException $e) {
            $ruleStatisticModel = $this->getNewInitializedRecord($ruleId);
        }
        return $ruleStatisticModel;
    }

    /**
     * Retrieve new initialized statistic record for rule
     *
     * @param int $ruleId
     * @return RuleStatisticInterface
     */
    private function getNewInitializedRecord($ruleId)
    {
        $ruleStatisticModel = $this->ruleStatisticFactory->create();
        $this->initializeRuleStatistic($ruleStatisticModel, $ruleId);
        return $ruleStatisticModel;
    }

    /**
     * Initialize statistic for rule without statistic data
     *
     * @param RuleStatisticInterface $ruleStatisticModel
     * @param int $ruleId
     * @param int $viewCount
     * @param int $clickCount
     * @return RuleStatisticInterface
     */
    private function initializeRuleStatistic($ruleStatisticModel, $ruleId, $viewCount = 0, $clickCount = 0)
    {
        $ruleStatisticModel->setRuleId($ruleId);
        $ruleStatisticModel->setViewCount($viewCount);
        $ruleStatisticModel->setClickCount($clickCount);
        return $ruleStatisticModel;
    }

    /**
     * Update rule statistic counter
     *
     * @param RuleStatisticInterface $ruleStatisticModel
     * @param string $counterKey
     * @return RuleStatisticInterface
     */
    private function updateRuleCounter($ruleStatisticModel, $counterKey)
    {
        return $ruleStatisticModel->setData($counterKey, $ruleStatisticModel->getData($counterKey) + 1);
    }
}
