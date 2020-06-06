<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wbtab\Indexer\Product;

use Aheadworks\Autorelated\Model\Wbtab\ResourceModel\Indexer\Product as ResourceProductIndexer;

/**
 * Class AbstractAction
 * @package Aheadworks\Autorelated\Model\Wbtab\Indexer\Product
 */
abstract class AbstractAction
{
    /**
     * @var ResourceProductIndexer
     */
    protected $resourceProductIndexer;

    /**
     * @param ResourceProductIndexer $resourceProductIndexer
     */
    public function __construct(
        ResourceProductIndexer $resourceProductIndexer
    ) {
        $this->resourceProductIndexer = $resourceProductIndexer;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return void
     */
    abstract public function execute($ids);
}
