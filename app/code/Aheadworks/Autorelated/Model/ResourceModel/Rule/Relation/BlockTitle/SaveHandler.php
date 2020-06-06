<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\BlockTitle;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\BlockTitle
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
        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName('aw_autorelated_rule_block_title');
        $connection->delete($tableName, ['rule_id = ?' => $entityId]);

        $blockTitleStoreValuesToInsert = [];
        /** @var RuleTitleStoreValueInterface $blockTitleStoreValueEntity */
        foreach ($entity->getTitleStoreValues() as $blockTitleStoreValueEntity) {
            if (!$blockTitleStoreValueEntity->getValue()) {
                continue;
            }
            $blockTitleStoreValuesToInsert[] = [
                'rule_id' => $entityId,
                'store_id' => $blockTitleStoreValueEntity->getStoreId(),
                'value' => $blockTitleStoreValueEntity->getValue()
            ];
        }
        if ($blockTitleStoreValuesToInsert) {
            $connection->insertMultiple($tableName, $blockTitleStoreValuesToInsert);
        }

        return $entity;
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
