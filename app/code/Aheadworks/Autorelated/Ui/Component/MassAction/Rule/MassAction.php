<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Ui\Component\MassAction\Rule;

use Magento\Ui\Component\MassAction as ComponentMassAction;

/**
 * Class MassAction
 *
 * @package Aheadworks\Autorelated\Ui\Component\MassAction\Rule
 */
class MassAction extends ComponentMassAction
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getConfiguration();
        $allowedActions = [];
        foreach ($config['actions'] as $action) {
            if ($this->ifActionAllowed($action)) {
                $allowedActions[] = $action;
            }
        }

        $config['actions'] = $allowedActions;
        $this->setData('config', (array)$config);
    }

    /**
     * Check if action is allowed to be shown in grid
     * @param array $action
     * @return bool
     */
    private function ifActionAllowed($action)
    {
        if (isset($action['actions']) && is_object($action['actions'])) {
            return $action['actions']->jsonSerialize() ? true : false;
        }
        return true;
    }
}
