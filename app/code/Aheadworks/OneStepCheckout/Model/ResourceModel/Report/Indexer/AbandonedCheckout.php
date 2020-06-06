<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Indexer;

use Aheadworks\OneStepCheckout\Model\Report\Aggregation;
use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Aggregation\PeriodProviderPool;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Indexer\Model\ResourceModel\AbstractResource;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbandonedCheckout
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Indexer
 */
class AbandonedCheckout extends AbstractResource implements IndexerInterface
{
    /**
     * @var Aggregation
     */
    private $aggregation;

    /**
     * @var PeriodProviderPool
     */
    private $periodProviderPool;

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    /**
     * @param Context $context
     * @param StrategyInterface $tableStrategy
     * @param Aggregation $aggregation
     * @param PeriodProviderPool $periodProviderPool
     * @param OrderResource $orderResource
     * @param QuoteResource $quoteResource
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StrategyInterface $tableStrategy,
        Aggregation $aggregation,
        PeriodProviderPool $periodProviderPool,
        OrderResource $orderResource,
        QuoteResource $quoteResource,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->aggregation = $aggregation;
        $this->orderResource = $orderResource;
        $this->quoteResource = $quoteResource;
        $this->periodProviderPool = $periodProviderPool;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_osc_report_abandoned_checkouts_index', 'index_id');
    }

    /**
     * {@inheritdoc}
     */
    public function reindexAll()
    {
        // todo review after split database solution is fully supported
        if (!$this->checkIfHostIsTheSame()) {
            throw new LocalizedException(
                __('Split databases on different host have been found. Aheadworks OSC indexing is skipped.')
            );
        }

        $this->tableStrategy->setUseIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $this->prepareIndex();
            $this->syncData();
            $this->prepareAggregations();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Prepare index
     *
     * @return $this
     * @throws \Zend_Db_Select_Exception
     */
    private function prepareIndex()
    {
        $defaultConnection = $this->getConnection();
        $checkoutConnection = $this->quoteResource->getConnection();
        $salesConnection = $this->orderResource->getConnection();

        $quoteSelect = $checkoutConnection->select()
            ->from(
                ['quote' => $this->quoteResource->getTable('quote')],
                [
                    'abandoned_cart' => 'quote.entity_id',
                    'abandoned_cart_total' => 'quote.base_grand_total',
                    'completed_cart' => new \Zend_Db_Expr('NULL'),
                    'completed_cart_total' => new \Zend_Db_Expr('0'),
                    'created_at' => $checkoutConnection->getCheckSql(
                        'quote.created_at > quote.updated_at',
                        'quote.created_at',
                        'quote.updated_at'
                    ),
                    'store_id' => 'quote.store_id',
                    'customer_group_id' => 'quote.customer_group_id',
                    'base_to_global_rate' => 'quote.base_to_global_rate'
                ],
                $checkoutConnection->getConfig()['dbname']
            )
            ->join(
                ['completeness' => $this->quoteResource->getTable('aw_osc_checkout_data_completeness')],
                'quote.entity_id = completeness.quote_id',
                [],
                $checkoutConnection->getConfig()['dbname']
            )
            ->where('(quote.is_active = 1) AND (quote.items_count > 0)')
            ->group('completeness.quote_id');
        $orderSelect = $salesConnection->select()
            ->from(
                ['order' => $this->orderResource->getTable('sales_order')],
                [
                    'abandoned_cart' => new \Zend_Db_Expr('NULL'),
                    'abandoned_cart_total' => new \Zend_Db_Expr('0'),
                    'completed_cart' => 'order.quote_id',
                    'completed_cart_total' => 'order.base_grand_total',
                    'created_at' => 'order.created_at',
                    'store_id' => 'order.store_id',
                    'customer_group_id' => 'order.customer_group_id',
                    'base_to_global_rate' => 'order.base_to_global_rate'
                ],
                $salesConnection->getConfig()['dbname']
            )
            ->join(
                ['completeness' => $this->quoteResource->getTable('aw_osc_checkout_data_completeness')],
                'order.quote_id = completeness.quote_id',
                [],
                $checkoutConnection->getConfig()['dbname']
            )
            ->group('completeness.quote_id');
        $subSelect = $checkoutConnection->select()->union([$quoteSelect, $orderSelect]);

        $periodExpr = new \Zend_Db_Expr('DATE(main_table.created_at)');
        $abandonedCheckoutsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(abandoned_cart), 0)');
        $completedCheckoutsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(completed_cart), 0)');
        $select = $defaultConnection->select()
            ->from(
                ['main_table' => $subSelect],
                [
                    'period' => $periodExpr,
                    'store_id' => 'main_table.store_id',
                    'customer_group_id' => 'main_table.customer_group_id',
                    'abandoned_checkouts_count' => $abandonedCheckoutsCountExpr,
                    'abandoned_checkouts_revenue' => new \Zend_Db_Expr('COALESCE(SUM(abandoned_cart_total), 0)'),
                    'completed_checkouts_count' => $completedCheckoutsCountExpr,
                    'completed_checkouts_revenue' => new \Zend_Db_Expr('COALESCE(SUM(completed_cart_total), 0)'),
                    'conversion' => new \Zend_Db_Expr(
                        'COALESCE((100 / (' . $abandonedCheckoutsCountExpr . ' + ' . $completedCheckoutsCountExpr
                        . ')) * ' . $completedCheckoutsCountExpr . ', 0)'
                    ),
                    'base_to_global_rate' => 'main_table.base_to_global_rate'
                ]
            )
            ->group([$periodExpr, 'store_id', 'customer_group_id', 'base_to_global_rate']);

        $defaultConnection->query(
            $select->insertFromSelect(
                $this->getIdxTable(),
                [
                    'period',
                    'store_id',
                    'customer_group_id',
                    'abandoned_checkouts_count',
                    'abandoned_checkouts_revenue',
                    'completed_checkouts_count',
                    'completed_checkouts_revenue',
                    'conversion',
                    'base_to_global_rate'
                ]
            )
        );

        return $this;
    }

    /**
     * todo: consider move to a separate index
     * Prepare aggregation tables
     *
     * @return $this
     * @throws \Exception
     */
    private function prepareAggregations()
    {
        $connection = $this->getConnection();

        $dateRangeSelect = $connection->select()
            ->from(
                ['main_table' => $this->getMainTable()],
                [
                    'from' => new \Zend_Db_Expr('MIN(period)'),
                    'to' => new \Zend_Db_Expr('MAX(period)')
                ]
            );
        $range = $connection->fetchRow($dateRangeSelect);
        if ($range) {
            foreach ($this->aggregation->getAggregations() as $aggregation) {
                $aggregationTable = $this->getTable('aw_osc_report_aggregation_by_' . $aggregation);
                $connection->delete($aggregationTable);
                $periods = $this->periodProviderPool->getProvider($aggregation)
                    ->getPeriods($range['from'], $range['to']);

                $data = [];
                foreach ($periods as $period) {
                    $data[] = [
                        'period_from' => $period['from'],
                        'period_to' => $period['to']
                    ];
                }
                $connection->insertMultiple($aggregationTable, $data);
            }
        }

        return $this;
    }

    /**
     * Check if split databases are located on the same host
     *
     * @return bool
     */
    private function checkIfHostIsTheSame()
    {
        $defaultConnection = $this->getConnection();
        $checkoutConnection = $this->quoteResource->getConnection();
        $salesConnection = $this->orderResource->getConnection();

        return $defaultConnection->getConfig()['host'] == $checkoutConnection->getConfig()['host']
            && $defaultConnection->getConfig()['host'] == $salesConnection->getConfig()['host'];
    }
}
