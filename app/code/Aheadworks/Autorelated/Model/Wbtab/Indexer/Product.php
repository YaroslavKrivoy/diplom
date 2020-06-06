<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wbtab\Indexer;

/**
 * Class Product
 * @package Aheadworks\Autorelated\Model\Wbtab\Indexer
 */
class Product implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var Product\Action\Row
     */
    private $productIndexerRow;

    /**
     * @var Product\Action\Rows
     */
    private $productIndexerRows;

    /**
     * @var Product\Action\Full
     */
    private $productIndexerFull;

    /**
     * @param Product\Action\Row $productIndexerRow
     * @param Product\Action\Rows $productIndexerRows
     * @param Product\Action\Full $productIndexerFull
     */
    public function __construct(
        Product\Action\Row $productIndexerRow,
        Product\Action\Rows $productIndexerRows,
        Product\Action\Full $productIndexerFull
    ) {
        $this->productIndexerRow = $productIndexerRow;
        $this->productIndexerRows = $productIndexerRows;
        $this->productIndexerFull = $productIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function execute($ids)
    {
        $this->productIndexerRows->execute($ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->productIndexerFull->execute();
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     *
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->productIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     *
     * @return void
     */
    public function executeRow($id)
    {
        $this->productIndexerRow->execute($id);
    }
}
