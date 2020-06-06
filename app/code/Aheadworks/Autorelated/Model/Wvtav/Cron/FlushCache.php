<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wvtav\Cron;

use Aheadworks\Autorelated\Model\CacheManager;
use Psr\Log\LoggerInterface;

/**
 * Class FlushCache
 *
 * @package \Aheadworks\Autorelated\Model\Wvtav\Cron
 */
class FlushCache
{
    /**
     * Cache manager
     *
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Class constructor
     *
     * @param CacheManager $cacheManager
     * @param LoggerInterface $logger
     */
    public function __construct(CacheManager $cacheManager, LoggerInterface $logger)
    {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
    }

    /**
     * Execute cron command
     *
     * @return \Aheadworks\Autorelated\Model\Wvtav\Cron\FlushCache
     */
    public function execute()
    {
        try {
            $this->cacheManager->flushCacheForWvtavIfNeeded();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
