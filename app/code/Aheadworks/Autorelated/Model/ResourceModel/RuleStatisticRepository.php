<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel;

use Aheadworks\Autorelated\Api\Data\RuleStatisticInterface;
use Aheadworks\Autorelated\Model\RuleStatisticFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Rule statistic repository
 */
class RuleStatisticRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RuleStatisticFactory
     */
    private $ruleStatisticFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $ruleStatisticObjectsRegistry = [];

    /**
     * @param EntityManager $entityManager
     * @param RuleStatisticFactory $ruleStatisticFactory
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        EntityManager $entityManager,
        RuleStatisticFactory $ruleStatisticFactory,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->entityManager = $entityManager;
        $this->ruleStatisticFactory = $ruleStatisticFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save rule statistics
     *
     * @param RuleStatisticInterface $ruleStatistic
     * @return RuleStatisticInterface
     * @throws LocalizedException
     */
    public function save(RuleStatisticInterface $ruleStatistic)
    {
        /** @var \Aheadworks\Autorelated\Model\RuleStatistic $ruleStatisticModel */
        $ruleStatisticModel = $this->ruleStatisticFactory->create();
        if ($ruleId = $ruleStatistic->getRuleId()) {
            $this->entityManager->load($ruleStatisticModel, $ruleId);
        }
        $ruleStatisticModel->addData(
            $this->dataObjectProcessor->buildOutputDataArray(
                $ruleStatistic,
                RuleStatisticInterface::class
            )
        );
        $this->entityManager->save($ruleStatisticModel);
        $this->pushObjectToRegistry($ruleStatisticModel);
        return $ruleStatisticModel;
    }

    /**
     * Retrieve rule statistic
     *
     * @param int $ruleId
     * @return RuleStatisticInterface
     * @throws NoSuchEntityException
     */
    public function get($ruleId)
    {
        if (null === $this->retrieveObjectFromRegistry($ruleId)) {
            /** @var \Aheadworks\Autorelated\Model\RuleStatistic $ruleStatisticModel */
            $ruleStatisticModel = $this->ruleStatisticFactory->create();
            $this->entityManager->load($ruleStatisticModel, $ruleId);
            if (!$ruleStatisticModel->getRuleId()) {
                throw NoSuchEntityException::singleField('ruleId', $ruleId);
            } else {
                $this->pushObjectToRegistry($ruleStatisticModel);
            }
        }
        return $this->retrieveObjectFromRegistry($ruleId);
    }

    /**
     * Delete rule statistic
     *
     * @param RuleStatisticInterface $ruleStatistic
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(RuleStatisticInterface $ruleStatistic)
    {
        return $this->deleteById($ruleStatistic->getRuleId());
    }

    /**
     * Delete rule statistic by rule ID
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($ruleId)
    {
        $ruleStatisticModel = $this->retrieveObjectFromRegistry($ruleId);
        if (null === $ruleStatisticModel) {
            /** @var \Aheadworks\Autorelated\Model\RuleStatistic $ruleStatisticModel */
            $ruleStatisticModel = $this->ruleStatisticFactory->create();
            $this->entityManager->load($ruleStatisticModel, $ruleId);
            if (!$ruleStatisticModel->getRuleId()) {
                throw NoSuchEntityException::singleField('ruleId', $ruleId);
            }
        }
        $this->entityManager->delete($ruleStatisticModel);
        $this->removeObjectFromRegistry($ruleId);
        return true;
    }

    /**
     * Retrieve rule statistic record from the inner registry
     *
     * @param $ruleId
     * @return RuleStatisticInterface
     */
    private function retrieveObjectFromRegistry($ruleId)
    {
        $ruleStatistic = null;
        if (isset($this->ruleStatisticObjectsRegistry[$ruleId])) {
            $ruleStatistic = $this->ruleStatisticObjectsRegistry[$ruleId];
        }
        return $ruleStatistic;
    }

    /**
     * Push rule statistic record to the inner registry
     *
     * @param RuleStatisticInterface $ruleStatistic
     * @return $this
     */
    private function pushObjectToRegistry(RuleStatisticInterface $ruleStatistic)
    {
        if ($ruleId = $ruleStatistic->getId()) {
            $this->ruleStatisticObjectsRegistry[$ruleId] = $ruleStatistic;
        }
        return $this;
    }

    /**
     * Remove rule statistic record from the inner registry
     *
     * @param $ruleId
     */
    private function removeObjectFromRegistry($ruleId)
    {
        if (isset($this->ruleStatisticObjectsRegistry[$ruleId])) {
            unset($this->ruleStatisticObjectsRegistry[$ruleId]);
        }
    }
}
