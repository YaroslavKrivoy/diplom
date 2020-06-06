<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Rule\Relation\WvtavParams;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Autorelated\Api\Data\RuleInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 * @package Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Rule\Relation\WvtavParams
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
        $wvtavParamsRow = [
            'suggest_only_one_category' => (!$entity->getWvtavProductConditionIsSuggestOnlyOneCategory())
                ? 0
                : $entity->getWvtavProductConditionIsSuggestOnlyOneCategory()
            ,
            'suggest_only_price_higher' => (!$entity->getWvtavProductConditionIsSuggestOnlyPriceHigher())
                ? 0
                : $entity->getWvtavProductConditionIsSuggestOnlyPriceHigher()
        ];
        $origWvtavParamsRow = $this->getWvtavParams($entityId);

        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName('aw_autorelated_rule_wvtav_params');

        if (is_array($origWvtavParamsRow)
            && isset($origWvtavParamsRow['suggest_only_one_category'])
            && isset($origWvtavParamsRow['suggest_only_price_higher'])
        ) {
            $connection->update($tableName, $wvtavParamsRow, ['rule_id = ?' => $entityId]);
        } else {
            $wvtavParamsRow['rule_id'] = (int)$entityId;
            $connection->insert($tableName, $wvtavParamsRow);
        }

        return $entity;
    }

    /**
     * Get WVTAV params data to which entity is assigned
     *
     * @param int $entityId
     * @return array
     */
    private function getWvtavParams($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->resourceConnection->getTableName('aw_autorelated_rule_wvtav_params'),
                ['suggest_only_one_category', 'suggest_only_price_higher']
            )->where('rule_id = :id');
        return $connection->fetchRow($select, ['id' => $entityId]);
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
