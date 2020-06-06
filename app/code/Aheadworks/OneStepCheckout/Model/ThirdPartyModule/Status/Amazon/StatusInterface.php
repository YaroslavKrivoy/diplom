<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon;

/**
 * Interface StatusInterface
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon
 */
interface StatusInterface
{
    /**
     * Check if Amazon Payment Enabled
     *
     * @return bool
     */
    public function isPwaEnabled();
}
