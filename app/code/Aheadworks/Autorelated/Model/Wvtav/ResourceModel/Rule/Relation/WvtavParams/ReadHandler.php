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
 * Class ReadHandler
 * @package Aheadworks\Autorelated\Model\Wvtav\ResourceModel\Rule\Relation\WvtavParams
 */
class ReadHandler implements ExtensionInterface
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
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from(
                    $this->resourceConnection->getTableName('aw_autorelated_rule_wvtav_params'),
                    ['suggest_only_one_category', 'suggest_only_price_higher']
                )->where('rule_id = :id');
            $row = $connection->fetchRow($select, ['id' => $entityId]);

            $isSuggestOnlyOneCategory = isset($row['suggest_only_one_category'])
                ? $row['suggest_only_one_category']
                : 0;
            $isSuggestOnlyPriceHigher = isset($row['suggest_only_price_higher'])
                ? $row['suggest_only_price_higher']
                : 0;
            $entity->setWvtavProductConditionIsSuggestOnlyOneCategory($isSuggestOnlyOneCategory);
            $entity->setWvtavProductConditionIsSuggestOnlyPriceHigher($isSuggestOnlyPriceHigher);
        }
        return $entity;
    }
}
