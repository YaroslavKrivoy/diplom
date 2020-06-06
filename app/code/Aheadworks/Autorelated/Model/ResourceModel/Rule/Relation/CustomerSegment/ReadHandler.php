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
 * Class ReadHandler
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\CustomerSegment
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
            if ($entityId = (int)$entity->getId()) {
                $connection = $this->resourceConnection->getConnectionByName(
                    $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
                );
                $select = $connection->select()
                    ->from(
                        $this->resourceConnection->getTableName('aw_autorelated_rule_customer_segment'),
                        'customer_segment_id'
                    )->where('rule_id = :id');
                $customerSegmentIds = $connection->fetchCol($select, ['id' => $entityId]);
                $entity->setCustomerSegmentIds($customerSegmentIds);
            }
        }
        return $entity;
    }
}
