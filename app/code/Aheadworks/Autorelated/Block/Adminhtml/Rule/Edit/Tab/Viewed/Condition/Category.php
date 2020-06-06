<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Viewed\Condition;

use Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\AbstractCondition;
use Aheadworks\Autorelated\Api\Data\RuleInterface;

/**
 * Class Category
 *
 * @package Aheadworks\Autorelated\Block\Adminhtml\Rule\Edit\Tab\Viewed\Condition
 */
class Category extends AbstractCondition
{
    /**
     * {@inheritdoc}
     */
    protected $_nameInLayout = 'viewed_condition_category';

    /**
     * {@inheritdoc}
     */
    protected function addFieldsToFieldset($fieldset, $conditionData)
    {
        $fieldset->addField(
            'category_ids',
            'hidden',
            [
                'name' => 'category_ids',
                'data-form-part' => self::FORM_NAME,
                'after_element_js' => '<script type="text/javascript">
                            сategoryIds = {updateElement : {value : "", linkedValue : ""}};
                            Object.defineProperty(сategoryIds.updateElement, "value", {
                                get: function() {
                                        return сategoryIds.updateElement.linkedValue
                                },
                                set: function(v) {
                                        сategoryIds.updateElement.linkedValue = v;
                                        jQuery("#rule_category_ids").val(v)
                                }
                            });
                        </script>',
                'value' => isset($conditionData) ? $conditionData : ''
            ]
        );

        $categoryTreeBlock = $this->getLayout()->createBlock(
            \Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree::class,
            null,
            ['data' => ['js_form_object' => "сategoryIds"]]
        );
        if (isset($conditionData)) {
            $categoryTreeBlock->setCategoryIds(explode(',', $conditionData));
        }

        $fieldset->addField(
            'category_tree_container',
            'note',
            [
                'label' => __('Category'),
                'title' => __('Category'),
                'text' => $categoryTreeBlock->toHtml()
                    . '<script type="text/javascript">jQuery("body").trigger("contentUpdated");</script>',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormDataConditionKey()
    {
        return RuleInterface::CATEGORY_IDS;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldsetName()
    {
        return 'viewed_category_conditions_fieldset';
    }
}
