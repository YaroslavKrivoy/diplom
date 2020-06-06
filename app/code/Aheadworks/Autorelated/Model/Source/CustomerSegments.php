<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Autorelated\Model\Config;

/**
 * Class CustomerSegments
 *
 * @package Aheadworks\Autorelated\Model\Source
 */
class CustomerSegments implements OptionSourceInterface
{
    /**
     * Name of enterprise segments collection class
     */
    const ENTERPRISE_SEGMENTS_COLLECTION_CLASS_NAME = '\Magento\CustomerSegment\Model\ResourceModel\Segment\Collection';

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            $optionArray = $this->getActiveCustomerSegmentsOptionArray();
        }
        return $optionArray;
    }

    /**
     * Get active segments
     *
     * @return array
     */
    private function getActiveCustomerSegmentsOptionArray()
    {
        $optionArray = [];
        $enterpriseSegmentsCollection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(self::ENTERPRISE_SEGMENTS_COLLECTION_CLASS_NAME);
        if (is_object($enterpriseSegmentsCollection)) {
            $enterpriseSegmentsCollection->addFieldToFilter('is_active', '1');
            $optionArray = $enterpriseSegmentsCollection->toOptionArray();
        }
        return $optionArray;
    }
}
