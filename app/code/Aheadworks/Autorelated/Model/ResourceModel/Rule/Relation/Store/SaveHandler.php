<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\Store;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\Store
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
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $storeIds = $entity->getStoreIds();
        $storeIdsOrig = $this->getStoreIds($entityId);

        $toInsert = array_diff($storeIds, $storeIdsOrig);
        $toDelete = array_diff($storeIdsOrig, $storeIds);

        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName('aw_autorelated_rule_store');

        if ($toInsert) {
            $data = [];
            foreach ($toInsert as $storeId) {
                $data[] = [
                    'rule_id' => (int)$entityId,
                    'store_id' => (int)$storeId,
                ];
            }
            $connection->insertMultiple($tableName, $data);
        }
        if (count($toDelete)) {
            $connection->delete(
                $tableName,
                ['rule_id = ?' => $entityId, 'store_id IN (?)' => $toDelete]
            );
        }
        return $entity;
    }

    /**
     * Get store IDs to which entity is assigned
     *
     * @param int $entityId
     * @return array
     */
    private function getStoreIds($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('aw_autorelated_rule_store'), 'store_id')
            ->where('rule_id = :id');
        return $connection->fetchCol($select, ['id' => $entityId]);
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
