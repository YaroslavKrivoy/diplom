<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\Module\ModuleListInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon\StatusInterface;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\AmazonVersionPool;

/**
 * Class Amazon
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class Amazon implements StatusInterface
{
    /**
     * Amazon Core Module
     */
    const AMAZON_MODULE_NAME = 'Amazon_Core';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var AmazonVersionPool
     */
    private $amazonVersionPool;

    /**
     * @param ModuleListInterface $moduleList
     * @param AmazonVersionPool $amazonVersionPool
     */
    public function __construct(
        ModuleListInterface $moduleList,
        AmazonVersionPool $amazonVersionPool
    ) {
        $this->moduleList = $moduleList;
        $this->amazonVersionPool = $amazonVersionPool;
    }

    /**
     * Check if current amazon version is declared in version processors
     *
     * @return bool
     */
    public function isPwaEnabled()
    {
        return $this->getProcessor()->isPwaEnabled();
    }

    /**
     * Get current Amazon module version
     *
     * @return mixed
     */
    private function getCurrentAmazonVersion()
    {
        return $this->moduleList
            ->getOne(self::AMAZON_MODULE_NAME)['setup_version'];
    }

    /**
     * Check if Amazon module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleList->has(self::AMAZON_MODULE_NAME);
    }

    /**
     * Get Amazon Version Processor
     *
     * @return StatusInterface
     */
    private function getProcessor()
    {
        return $this->amazonVersionPool->getAmazonVersionProcessor($this->getCurrentAmazonVersion());
    }
}
