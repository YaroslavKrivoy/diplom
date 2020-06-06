<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Viewed\Condition;

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
use Aheadworks\Autorelated\Model\Rule\Viewed\Product as ViewedProductRule;
use Aheadworks\Autorelated\Model\Rule\Viewed\ProductFactory as ViewedProductRuleFactory;
use Aheadworks\Autorelated\Api\Data\RuleInterface;

/**
 * Class Product
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Viewed\Condition
 */
class Product extends AbstractRuleBasedCondition
{
    /**
     * @var ViewedProductRuleFactory
     */
    private $viewedProductRuleFactory;

    /**
     * {@inheritdoc}
     */
    protected $_nameInLayout = 'viewed_condition_product';

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
     * @param ViewedProductRuleFactory $viewedProductRuleFactory
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
        ViewedProductRuleFactory $viewedProductRuleFactory,
        array $data = []
    ) {
        $this->viewedProductRuleFactory = $viewedProductRuleFactory;
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
        $viewedProductRule = $this->viewedProductRuleFactory->create();
        if (isset($conditionData) && (is_array($conditionData))) {
            $viewedProductRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionData);
        }
        return $viewedProductRule;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormDataConditionKey()
    {
        return RuleInterface::VIEWED_CONDITION;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldsetName()
    {
        return 'viewed_product_conditions_fieldset';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionPrefix()
    {
        return 'viewed';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionRuleClassName()
    {
        return ViewedProductRule::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFieldName()
    {
        return 'viewed_conditions';
    }
}
