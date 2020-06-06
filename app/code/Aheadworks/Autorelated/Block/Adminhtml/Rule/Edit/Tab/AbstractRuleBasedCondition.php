<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Rule\Model\Condition\AbstractCondition as RuleAbstractCondition;

/**
 * Class AbstractRuleBasedCondition
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractRuleBasedCondition extends AbstractCondition
{
    /**
     * {@inheritdoc}
     */
    protected function prepareFieldset($fieldset)
    {
        $conditionData = $this->getConditionData();
        $conditionRule = $this->getConditionRule($conditionData);
        $fieldset->setRenderer($this->getFieldsetRenderer());
        $conditionRule->setJsFormObject($this->getFormHtmlIdPrefix() . $this->getFieldsetName());
        $this->addFieldsToFieldset($fieldset, $conditionRule);
        $this->setConditionFormName($conditionRule->getConditions(), self::FORM_NAME);
    }

    /**
     * Retrieve condition rule object from condition array
     *
     * @param mixed $conditionData
     * @return \Magento\Rule\Model\AbstractModel
     */
    abstract protected function getConditionRule($conditionData);

    /**
     * Retrieve renderer for form fieldset
     *
     * @return \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
     */
    protected function getFieldsetRenderer()
    {
        return $this->rendererFieldsetFactory->create()
            ->setTemplate($this->getFieldsetTemplate())
            ->setNewChildUrl(
                $this->getUrl(
                    $this->getFieldsetNewChildUrlRoute(),
                    [
                        'form'   => $this->getFormHtmlIdPrefix() . $this->getFieldsetName(),
                        'prefix' => $this->getConditionPrefix(),
                        'rule'   => base64_encode($this->getConditionRuleClassName()),
                        'form_namespace' => self::FORM_NAME
                    ]
                )
            );
    }

    /**
     * Add necessary fields to form fieldset
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param mixed $conditionData
     */
    protected function addFieldsToFieldset($fieldset, $conditionData)
    {
        $fieldset
            ->addField(
                $this->getConditionFieldName(),
                'text',
                [
                    'name' => $this->getConditionFieldName(),
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'data-form-part' => self::FORM_NAME
                ]
            )
            ->setRule($conditionData)
            ->setRenderer($this->conditions);
    }

    /**
     * Handles addition of form name to condition and its conditions
     *
     * @param RuleAbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    protected function setConditionFormName(RuleAbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }

    /**
     * Retrieve fieldset template
     *
     * @return string
     */
    protected function getFieldsetTemplate()
    {
        return 'Magento_CatalogRule::promo/fieldset.phtml';
    }

    /**
     * Retrieve url route of fieldset new child
     *
     * @return string
     */
    protected function getFieldsetNewChildUrlRoute()
    {
        return '*/*/newConditionHtml';
    }

    /**
     * Retrieve condition prefix
     *
     * @return string
     */
    abstract protected function getConditionPrefix();

    /**
     * Retrieve name of condition rule class
     *
     * @return string
     */
    abstract protected function getConditionRuleClassName();

    /**
     * Retrieve condition field name
     *
     * @return string
     */
    abstract protected function getConditionFieldName();
}
