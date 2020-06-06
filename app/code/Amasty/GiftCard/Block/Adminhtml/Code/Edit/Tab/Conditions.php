<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Block\Adminhtml\Code\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Rule\Block\Conditions as BlockConditions;
use Amasty\GiftCard\Model\RuleFactory;
use Magento\Framework\Phrase;

class Conditions extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    private $rendererFieldset;

    /**
     * @var BlockConditions
     */
    private $blockConditions;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Fieldset $rendererFieldset,
        BlockConditions $blockConditions,
        RuleFactory $ruleFactory,
        array $data = []
    ){
        $this->rendererFieldset = $rendererFieldset;
        $this->blockConditions = $blockConditions;
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\SalesRule\Model\Rule $rule */
        $rule = $this->ruleFactory->create();

        $model = $this->_coreRegistry->registry('current_amasty_giftcard_code');
        if ($model) {
            $conditions = $model->getConditionsSerialized();
            $rule->setConditionsSerialized($conditions);
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('sales_rule/promo_quote/newConditionHtml/form')
        );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __("Conditions (don't specify conditions if you'd the like rule to be applied to all products)")]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions'), 'required' => true]
        )->setRule(
            $rule
        )->setRenderer(
            $this->blockConditions
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
