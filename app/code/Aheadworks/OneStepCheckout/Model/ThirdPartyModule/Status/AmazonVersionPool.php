<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class AmazonVersionPool
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status
 */
class AmazonVersionPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $versionProcessors = [];

    /**
     * @var StatusInterface[]
     */
    private $processorInstance = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $versionProcessors
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $versionProcessors = []
    ) {
        $this->objectManager = $objectManager;
        $this->versionProcessors = $versionProcessors;
    }

    /**
     * Get Amazon version processor according to module version
     *
     * @param $moduleVersion
     * @return StatusInterface
     * @throws \Exception
     */
    public function getAmazonVersionProcessor($moduleVersion)
    {
        if (isset($this->processorInstance[$moduleVersion])) {
            return $this->processorInstance[$moduleVersion];
        }

        if (isset($this->versionProcessors[$moduleVersion])) {
            if ($this->isAmazonVersionProcessorCanBeCreated($this->versionProcessors[$moduleVersion])) {
                $this->processorInstance[$moduleVersion]
                    = $this->objectManager->create($this->versionProcessors[$moduleVersion]);
                return $this->processorInstance[$moduleVersion];
            } else {
                throw new \Exception(
                    sprintf('Class not found %s', $this->versionProcessors[$moduleVersion])
                );
            }
        } elseif (!isset($this->processorInstance['default'])) {
            if ($this->isAmazonVersionProcessorCanBeCreated($this->versionProcessors['default'])) {
                $this->processorInstance['default']
                    = $this->objectManager->create($this->versionProcessors['default']);
                return $this->processorInstance['default'];
            }
        } else {
            return $this->processorInstance['default'];
        }
    }

    /**
     * Check if Amazon version processor can be created
     *
     * @param $amazonVersionProcessor
     * @return bool
     */
    private function isAmazonVersionProcessorCanBeCreated($amazonVersionProcessor)
    {
        return class_exists($amazonVersionProcessor);
    }
}
