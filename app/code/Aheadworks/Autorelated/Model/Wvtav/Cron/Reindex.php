<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wvtav\Cron;

use Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer\Wvtav as WvtavIndexerModel;
use Psr\Log\LoggerInterface;

/**
 * Class Reindex
 *
 * @package \Aheadworks\Autorelated\Model\Wvtav\Cron
 */
class Reindex
{
    /**
     * Indexer
     *
     * @var WvtavIndexerModel
     */
    private $indexer = null;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Class constructor
     *
     * @param WvtavIndexerModel $indexer
     * @param LoggerInterface $logger
     */
    public function __construct(WvtavIndexerModel $indexer, LoggerInterface $logger)
    {
        $this->indexer = $indexer;
        $this->logger = $logger;
    }

    /**
     * Execute cron command
     *
     * @return \Aheadworks\Autorelated\Model\Wvtav\Cron\Reindex
     */
    public function execute()
    {
        try {
            $this->indexer->reindexAllIfNeeded();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $this;
    }
}
