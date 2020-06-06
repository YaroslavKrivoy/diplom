<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer;

use Aheadworks\Autorelated\Model\Config;

/**
 * Class Wvtav
 * @package \Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer
 */
class Wvtav extends \Magento\Indexer\Model\ResourceModel\AbstractResource
{
    /**#@+
     *
     * Default index period (days)
     */
    const DEFAULT_INDEX_PERIOD = 90;
    /**#@-*/

    /**
     * Module config
     *
     * @var Config
     */
    protected $config;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy
     * @param Config $config
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        Config $config,
        $connectionName = null
    ) {
        $this->config = $config;
        parent::__construct($context, $tableStrategy, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_autorelated_wvtav_index', 'index_id');
    }

    /**
     * Reindex all data if needed
     *
     * @return \Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer\Wvtav
     */
    public function reindexAllIfNeeded()
    {
        if ($this->isNeedToReindexAll()) {
            $this->reindexAll();
        }
        return $this;
    }

    /**
     * Check if need to start reindex
     *
     * @return bool
     */
    protected function isNeedToReindexAll()
    {
        return ($this->config->isWvtavFunctionalityEnabled());
    }

    /**
     * Reindex all data
     *
     * @return \Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer\Wvtav
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->beginTransaction();
        try {
            $this->prepareWvtavIndex();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Return the period for indexer (days)
     *
     * @return int
     */
    protected function getIndexPeriod()
    {
        $indexPeriodFromConfig = $this->config->getWvtavProcessSessionsPeriod();
        return (empty($indexPeriodFromConfig) ? self::DEFAULT_INDEX_PERIOD : $indexPeriodFromConfig);
    }

    /**
     * Clear all previous data
     *
     * @return \Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer\Wvtav
     */
    protected function prepareWvtavIndexTable()
    {
        $this->getConnection()->delete($this->getWvtavIndexTable());
        return $this;
    }

    /**
     * Collect sql and execute query for index the data
     *
     * @return \Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer\Wvtav
     */
    protected function prepareWvtavIndex()
    {
        $this->prepareWvtavIndexTable();

        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from(
            ['v1' => $this->getReportViewProductViewIndexTable()],
            []
        )->joinInner(
            ['v2' => $this->getReportViewProductViewIndexTable()],
            'v1.customer_id=v2.customer_id or v1.visitor_id=v2.visitor_id',
            []
        )->where('v1.product_id <> v2.product_id');

        $productsPairSql = new \Zend_Db_Expr('CONCAT(v1.product_id, v2.product_id)');
        $select->columns(
            [
                'master_product_id' => 'v1.product_id',
                'slave_product_id' => 'v2.product_id',
                'rating' => new \Zend_Db_Expr('COUNT(' . $productsPairSql . ')'),
            ]
        );

        $select->group($productsPairSql);

        $this->addSessionPeriod($select, $this->getIndexPeriod());

        $query = $select->insertFromSelect(
            $this->getWvtavIndexTable(),
            ['master_product_id', 'slave_product_id', 'rating']
        );

        $connection->query($query);

        return $this;
    }

    /**
     * Add session period for select
     *
     * @param \Magento\Framework\DB\Select $select
     * @param int $days
     * @return \Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Indexer\Wvtav
     */
    protected function addSessionPeriod($select, $days)
    {
        if ($days > 0) {
            $select->where('v1.added_at BETWEEN NOW() - INTERVAL ? DAY AND NOW()', $days);
        }
        return $this;
    }

    /**
     * Return report viewed product table name
     *
     * @return string
     */
    protected function getReportViewProductViewIndexTable()
    {
        return $this->getTable('report_viewed_product_index');
    }

    /**
     * Return wvtav index table name
     *
     * @return string
     */
    protected function getWvtavIndexTable()
    {
        return $this->getTable('aw_autorelated_wvtav_index');
    }
}
