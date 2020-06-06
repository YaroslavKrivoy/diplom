<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Autorelated\Model\ResourceModel\Validator;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\TypeResolver;

/**
 * Class CodeIsUnique
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Validator
 */
class CodeIsUnique
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param MetadataPool $metadataPool
     * @param TypeResolver $typeResolver
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        TypeResolver $typeResolver,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->typeResolver = $typeResolver;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Check unique rule code
     *
     * @param object $entity
     * @return bool
     */
    public function validate($entity)
    {
        $entityType = $this->typeResolver->resolve($entity);
        $metaData = $this->metadataPool->getMetadata($entityType);
        $connection = $this->resourceConnection->getConnectionByName($metaData->getEntityConnectionName());

        $bind = ['code' => $entity->getCode()];
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('aw_autorelated_rule'))
            ->where('code = :code');
        if ($entity->getId()) {
            $select->where($metaData->getIdentifierField() . ' <> :id');
            $bind['id'] = $entity->getId();
        }
        if ($connection->fetchRow($select, $bind)) {
            return false;
        }

        return true;
    }
}
