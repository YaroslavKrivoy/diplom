<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wvtav\Config\Backend;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\Autorelated\Model\RuleStatusManager;

/**
 * Class EnableFunctionality
 * @package Aheadworks\Autorelated\Model\Wvtav\Config\Backend
 */
class EnableFunctionality extends \Magento\Framework\App\Config\Value
{
    /**
     * Const for "No" value in the 'Enable "WVTAV" functionality' selector
     */
    const DISABLED_VALUE = 0;

    /**
     * @var RuleStatusManager
     */
    private $ruleStatusManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param RuleStatusManager $ruleStatusManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        RuleStatusManager $ruleStatusManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ruleStatusManager = $ruleStatusManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        $this->disableConnectedRulesIfWvtavFunctionalitySwitchedToDisabled();
        return parent::afterSave();
    }

    /**
     * Disable if needed rules, connected to WVTAV functionality
     *
     * @return void
     */
    private function disableConnectedRulesIfWvtavFunctionalitySwitchedToDisabled()
    {
        if ($this->isWvtavFunctionalitySwitchedToDisabled()) {
            $this->ruleStatusManager->disableRulesConnectedToWvtavFunctionality();
        }
    }

    /**
     * Check if option was changed to disabled value
     *
     * @return bool
     */
    private function isWvtavFunctionalitySwitchedToDisabled()
    {
        return (((int)$this->getValue() == self::DISABLED_VALUE)
            && ((int)$this->getValue() != (int)$this->getOldValue())
        );
    }
}
