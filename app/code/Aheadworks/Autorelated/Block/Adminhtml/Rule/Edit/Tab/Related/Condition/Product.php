<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Related\Condition;

use Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\AbstractRuleBasedCondition;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Rule\Block\Conditions;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Autorelated\Api\RuleRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\DataObject;
use Aheadworks\Autorelated\Model\Rule\Related\Product as RelatedProductRule;
use Aheadworks\Autorelated\Model\Rule\Related\ProductFactory as RelatedProductRuleFactory;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Rule\TypeResolver as RuleTypeResolver;

/**
 * Class Product
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Related\Condition
 */
class Product extends AbstractRuleBasedCondition
{
    /**
     * @var RelatedProductRuleFactory
     */
    private $relatedProductRuleFactory;

    /**
     * @var RuleTypeResolver
     */
    protected $ruleTypeResolver;

    /**
     * {@inheritdoc}
     */
    protected $_nameInLayout = 'related_condition_product';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Conditions $conditions
     * @param FieldsetFactory $rendererFieldsetFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RuleRepositoryInterface $ruleRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObject $dataObject
     * @param RelatedProductRuleFactory $relatedProductRuleFactory
     * @param RuleTypeResolver $ruleTypeResolver
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Conditions $conditions,
        FieldsetFactory $rendererFieldsetFactory,
        DataPersistorInterface $dataPersistor,
        RuleRepositoryInterface $ruleRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObject $dataObject,
        RelatedProductRuleFactory $relatedProductRuleFactory,
        RuleTypeResolver $ruleTypeResolver,
        array $data = []
    ) {
        $this->relatedProductRuleFactory = $relatedProductRuleFactory;
        $this->ruleTypeResolver = $ruleTypeResolver;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $conditions,
            $rendererFieldsetFactory,
            $dataPersistor,
            $ruleRepository,
            $dataObjectProcessor,
            $dataObject,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionRule($conditionData)
    {
        $relatedProductRule = $this->relatedProductRuleFactory->create();
        if ($this->isConditionDataBelongsToRelatedProductRule()
            && isset($conditionData)
            && (is_array($conditionData))
        ) {
            $relatedProductRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionData);
        }
        return $relatedProductRule;
    }

    /**
     * Check if condition data relates to current condition rule type
     *
     * @return bool
     */
    private function isConditionDataBelongsToRelatedProductRule()
    {
        $result = true;
        $formData = $this->getFormData();
        if (is_array($formData) && (isset($formData[RuleInterface::POSITION]))) {
            $result = !(
                $this->ruleTypeResolver
                    ->isRulePositionUseCategoryRelatedProductCondition($formData[RuleInterface::POSITION])
            );
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormDataConditionKey()
    {
        return RuleInterface::PRODUCT_CONDITION;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldsetName()
    {
        return 'related_product_conditions_fieldset';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionPrefix()
    {
        return 'related';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionRuleClassName()
    {
        return RelatedProductRule::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFieldName()
    {
        return 'related_conditions';
    }
}
