<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class DefaultProcessor
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon
 */
class DefaultProcessor implements StatusInterface
{
    /**
     * AbstractHelper Amazon Core Helper Class
     */
    const HELPER_CLASS_NAME = 'Amazon\Core\Helper\Data';

    /**
     * @var \Amazon\Core\Helper\Data
     */
    private $amazonHelper;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->amazonHelper = $objectManager->create(self::HELPER_CLASS_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function isPwaEnabled()
    {
        return $this->amazonHelper->isPwaEnabled();
    }
}
