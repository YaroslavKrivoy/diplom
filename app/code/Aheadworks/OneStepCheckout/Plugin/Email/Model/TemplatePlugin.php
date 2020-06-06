<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Plugin\Email\Model;

use Aheadworks\OneStepCheckout\Model\Order\Email\Source\Variable as VariableSource;
use Magento\Email\Model\Template;

/**
 * Class TemplatePlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Email\Model
 */
class TemplatePlugin
{
    /**
     * @var VariableSource
     */
    private $variableSource;

    /**
     * @param VariableSource $variableSource
     */
    public function __construct(
        VariableSource $variableSource
    ) {
        $this->variableSource = $variableSource;
    }

    /**
     * Add additional variable options for order templates
     *
     * @param Template $subject
     * @param array $result
     * @return array
     */
    public function afterGetVariablesOptionArray($subject, $result)
    {
        if ($this->variableSource->isSalesOrderTemplate($subject->getOrigTemplateCode())) {
            $oscVariables = $this->variableSource->toOptionArray();
            $result['value'] = array_merge($result['value'], $oscVariables);
        }

        return $result;
    }
}
