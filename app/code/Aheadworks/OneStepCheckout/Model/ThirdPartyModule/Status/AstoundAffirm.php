<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class AstoundAffirm
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class AstoundAffirm
{
    /**
     * Module Name
     */
    const MODULE_NAME = 'Astound_Affirm';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleList->has(self::MODULE_NAME);
    }
}
