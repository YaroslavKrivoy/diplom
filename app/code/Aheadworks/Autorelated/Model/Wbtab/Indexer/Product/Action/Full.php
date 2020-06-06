<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wbtab\Indexer\Product\Action;

/**
 * Class Full
 * @package Aheadworks\Autorelated\Model\Wbtab\Indexer\Product\Action
 */
class Full extends \Aheadworks\Autorelated\Model\Wbtab\Indexer\Product\AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param array|int|null $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids = null)
    {
        try {
            $this->resourceProductIndexer->reindexAll();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}
