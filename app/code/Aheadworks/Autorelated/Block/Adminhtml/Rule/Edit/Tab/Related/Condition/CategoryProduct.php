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
use Aheadworks\Autorelated\Model\Rule\Related\CategoryProduct as RelatedCategoryProductRule;
use Aheadworks\Autorelated\Model\Rule\Related\CategoryProductFactory as RelatedCategoryProductRuleFactory;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Aheadworks\Autorelated\Model\Rule\TypeResolver as RuleTypeResolver;

/**
 * Class CategoryProduct
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Related\Condition
 */
class CategoryProduct extends AbstractRuleBasedCondition
{
    /**
     * @var RelatedCategoryProductRuleFactory
     */
    private $relatedCategoryProductRuleFactory;

    /**
     * @var RuleTypeResolver
     */
    protected $ruleTypeResolver;

    /**
     * {@inheritdoc}
     */
    protected $_nameInLayout = 'related_category_condition_product';

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
     * @param RelatedCategoryProductRuleFactory $relatedCategoryProductRuleFactory
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
        RelatedCategoryProductRuleFactory $relatedCategoryProductRuleFactory,
        RuleTypeResolver $ruleTypeResolver,
        array $data = []
    ) {
        $this->relatedCategoryProductRuleFactory = $relatedCategoryProductRuleFactory;
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
        $relatedCategoryProductRule = $this->relatedCategoryProductRuleFactory->create();
        if ($this->isConditionDataBelongsToRelatedCategoryProductRule()
            && isset($conditionData)
            && (is_array($conditionData))
        ) {
            $relatedCategoryProductRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionData);
        }
        return $relatedCategoryProductRule;
    }

    /**
     * Check if condition data relates to current condition rule type
     *
     * @return bool
     */
    private function isConditionDataBelongsToRelatedCategoryProductRule()
    {
        $result = true;
        $formData = $this->getFormData();
        if (is_array($formData) && (isset($formData[RuleInterface::POSITION]))) {
            $result = $this->ruleTypeResolver
                ->isRulePositionUseCategoryRelatedProductCondition($formData[RuleInterface::POSITION]);
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
        return 'related_category_product_conditions_fieldset';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionPrefix()
    {
        return 'category_related';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionRuleClassName()
    {
        return RelatedCategoryProductRule::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFieldName()
    {
        return 'category_related_conditions';
    }
}
