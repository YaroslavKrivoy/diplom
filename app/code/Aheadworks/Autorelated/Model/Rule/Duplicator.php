<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Rule;

use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Api\Data\RuleInterfaceFactory;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Aheadworks\Autorelated\Model\Rule\Duplicator\ObjectBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Duplicator
 * @package Aheadworks\Autorelated\Model\Rule
 */
class Duplicator
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var ObjectBuilder
     */
    private $objectBuilder;

    /**
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleInterfaceFactory $ruleDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ObjectBuilder $objectBuilder
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository,
        RuleInterfaceFactory $ruleDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        ObjectBuilder $objectBuilder
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->objectBuilder = $objectBuilder;
    }

    /**
     * Create rule duplicate
     *
     * @param RuleInterface $rule
     * @return RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function duplicate($rule)
    {
        $duplicateRule = $this->ruleDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $duplicateRule,
            $this->dataObjectProcessor->buildOutputDataArray($rule, RuleInterface::class),
            RuleInterface::class
        );
        $duplicateRule = $this->objectBuilder->build($rule, $duplicateRule);

        $isDuplicateRuleSaved = false;
        do {
            try {
                $duplicateRule->setCode($this->generateNewCode($duplicateRule->getCode()));
                $duplicateRule = $this->ruleRepository->save($duplicateRule);
                $isDuplicateRuleSaved = true;
            } catch (AlreadyExistsException $e) {
            }
        } while (!$isDuplicateRuleSaved);

        return $duplicateRule;
    }

    /**
     * Generate new unique code
     *
     * @param string $code
     * @return string
     */
    private function generateNewCode($code)
    {
        return preg_match('/(.*)-(\d+)$/', $code, $matches)
            ? $matches[1] . '-' . ($matches[2] + 1)
            : $code . '-1';
    }
}
