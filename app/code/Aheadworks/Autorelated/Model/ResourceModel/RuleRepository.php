<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterfaceFactory;
use Aheadworks\Autorelated\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Autorelated\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Autorelated\Model\RuleFactory;
use Aheadworks\Autorelated\Model\RuleRegistry;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Autorelated\Model\Converter\Condition as ConditionConverter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Autorelated\Model\Serialize\SerializeInterface;
use Aheadworks\Autorelated\Model\Serialize\Factory as SerializeFactory;
use Magento\Store\Model\Store;

/**
 * Rule repository
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleRepository implements \Aheadworks\Autorelated\Api\RuleRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleDataFactory;

    /**
     * @var RuleRegistry
     */
    private $ruleRegistry;

    /**
     * @var RuleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var SerializeInterface
     */
    private $serializer;

    /**
     * @param EntityManager $entityManager
     * @param RuleFactory $ruleFactory
     * @param RuleInterfaceFactory $ruleDataFactory
     * @param RuleRegistry $ruleRegistry
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ConditionConverter $conditionConverter
     * @param SerializeFactory $serializeFactory
     */
    public function __construct(
        EntityManager $entityManager,
        RuleFactory $ruleFactory,
        RuleInterfaceFactory $ruleDataFactory,
        RuleRegistry $ruleRegistry,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ConditionConverter $conditionConverter,
        SerializeFactory $serializeFactory
    ) {
        $this->entityManager = $entityManager;
        $this->ruleFactory = $ruleFactory;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->ruleRegistry = $ruleRegistry;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->conditionConverter = $conditionConverter;
        $this->serializer = $serializeFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function save(RuleInterface $rule)
    {
        /** @var \Aheadworks\Autorelated\Model\Rule $ruleModel */
        $ruleModel = $this->ruleFactory->create();
        if ($ruleId = $rule->getId()) {
            $this->entityManager->load($ruleModel, $ruleId);
        }
        $ruleModel->addData(
            $this->dataObjectProcessor->buildOutputDataArray($rule, RuleInterface::class)
        );

        $ruleModel->beforeSave();
        $this->entityManager->save($ruleModel);
        $rule = $this->getRuleDataObject($ruleModel);
        $this->ruleRegistry->push($rule);
        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId, $storeId = null)
    {
        if (null === $this->ruleRegistry->retrieve($ruleId)) {
            /** @var Rule $rule */
            $rule = $this->ruleDataFactory->create();
            $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
            $this->entityManager->load($rule, $ruleId, ['store_id' => $storeId]);
            if (!$rule->getId()) {
                throw NoSuchEntityException::singleField('ruleId', $ruleId);
            } else {
                $rule = $this->convertRuleConditionsToDataModel($rule);
                $this->ruleRegistry->push($rule);
            }
        }

        return $this->ruleRegistry->retrieve($ruleId);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var RuleSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Autorelated\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, RuleInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == RuleInterface::STORE_IDS) {
                    $collection->addStoreFilter($filter->getValue());
                } elseif ($filter->getField() == RuleInterface::CUSTOMER_GROUP_IDS) {
                    $collection->addCustomerGroupFilter($filter->getValue());
                } elseif ($filter->getField() == RuleInterface::CUSTOMER_SEGMENT_IDS) {
                    $collection->addCustomerSegmentFilter($filter->getValue());
                } elseif ($filter->getField() == RuleInterface::ID) {
                    $collection->addRuleIdFilter($filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
        $collection
            ->setStoreId($storeId)
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $rules = [];
        /** @var \Aheadworks\Autorelated\Model\Rule $ruleModel */
        foreach ($collection as $ruleModel) {
            $rules[] = $this->convertRuleConditionsToDataModel($ruleModel);
        }
        $searchResults->setItems($rules);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RuleInterface $rule)
    {
        return $this->deleteById($rule->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        $rule = $this->ruleRegistry->retrieve($ruleId);
        if (null === $rule) {
            /** @var Rule $rule */
            $rule = $this->ruleDataFactory->create();
            $this->entityManager->load($rule, $ruleId);
            if (!$rule->getId()) {
                throw NoSuchEntityException::singleField('ruleId', $ruleId);
            }
        }
        $this->entityManager->delete($rule);
        $this->ruleRegistry->remove($ruleId);
        return true;
    }

    /**
     * Creates rule data object using Rule Model
     *
     * @param \Aheadworks\Autorelated\Model\Rule $rule
     * @return RuleInterface
     */
    private function getRuleDataObject(\Aheadworks\Autorelated\Model\Rule $rule)
    {
        /** @var RuleInterface $ruleDataObject */
        $ruleDataObject = $this->ruleDataFactory->create();
        $rule = $this->convertRuleConditionsToDataModel($rule);
        $this->dataObjectHelper->populateWithArray(
            $ruleDataObject,
            $this->dataObjectProcessor->buildOutputDataArray($rule, RuleInterface::class),
            RuleInterface::class
        );
        return $ruleDataObject;
    }

    /**
     * Convert rule conditions from array to data model
     *
     * @param RuleInterface $rule
     * @return RuleInterface
     */
    private function convertRuleConditionsToDataModel(RuleInterface $rule)
    {
        if ($rule->getViewedCondition()) {
            $conditionArray = $this->serializer->unserialize($rule->getViewedCondition());
            $conditionDataModel = $this->conditionConverter
                ->arrayToDataModel($conditionArray);
            $rule->setViewedCondition($conditionDataModel);
        } else {
            $rule->setViewedCondition(null);
        }

        if ($rule->getProductCondition()) {
            $conditionArray = $this->serializer->unserialize($rule->getProductCondition());
            $conditionDataModel = $this->conditionConverter
                ->arrayToDataModel($conditionArray);
            $rule->setProductCondition($conditionDataModel);
        } else {
            $rule->setProductCondition(null);
        }

        return $rule;
    }
}
