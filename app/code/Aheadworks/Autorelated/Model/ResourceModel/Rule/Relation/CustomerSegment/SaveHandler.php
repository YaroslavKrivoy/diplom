<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\CustomerSegment;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Autorelated\Model\Config;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\CustomerSegment
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param Config $config
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        Config $config
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            $entityId = (int)$entity->getId();
            $customerSegmentIds = $entity->getCustomerSegmentIds();
            $customerSegmentIdsOrig = $this->getCustomerSegmentIds($entityId);

            if (empty($customerSegmentIds)) {
                $customerSegmentIds = [];
            }

            $toInsert = array_diff($customerSegmentIds, $customerSegmentIdsOrig);
            $toDelete = array_diff($customerSegmentIdsOrig, $customerSegmentIds);

            $connection = $this->getConnection();
            $tableName = $this->resourceConnection->getTableName('aw_autorelated_rule_customer_segment');

            if ($toInsert) {
                $data = [];
                foreach ($toInsert as $customerGroupId) {
                    $data[] = [
                        'rule_id' => (int)$entityId,
                        'customer_segment_id' => (int)$customerGroupId,
                    ];
                }
                $connection->insertMultiple($tableName, $data);
            }
            if (count($toDelete)) {
                $connection->delete(
                    $tableName,
                    ['rule_id = ?' => $entityId, 'customer_segment_id IN (?)' => $toDelete]
                );
            }
        }
        return $entity;
    }

    /**
     * Get customer segment IDs to which entity is assigned
     *
     * @param int $entityId
     * @return array
     */
    private function getCustomerSegmentIds($entityId)
    {
        $customerSegmentIds = [];
        if ($this->config->isEnterpriseCustomerSegmentInstalled()) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(
                    $this->resourceConnection->getTableName('aw_autorelated_rule_customer_segment'),
                    'customer_segment_id'
                )->where('rule_id = :id');
            $customerSegmentIds = $connection->fetchCol($select, ['id' => $entityId]);
        }
        return $customerSegmentIds;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
        );
    }
}
