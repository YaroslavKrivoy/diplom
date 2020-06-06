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
use Aheadworks\Autorelated\Api\Data\RuleTitleStoreValueInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Autorelated\Model\Rule\BlockTitle\StoreResolver as BlockTitleStoreResolver;
use Magento\Store\Model\Store;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Autorelated\Model\ResourceModel\Rule\Relation\BlockTitle
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RuleTitleStoreValueInterfaceFactory
     */
    private $blockTitleStoreValueFactory;

    /**
     * @var BlockTitleStoreResolver
     */
    private $blockTitleStoreResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param RuleTitleStoreValueInterfaceFactory $blockTitleStoreValueFactory
     * @param BlockTitleStoreResolver $blockTitleStoreResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        RuleTitleStoreValueInterfaceFactory $blockTitleStoreValueFactory,
        BlockTitleStoreResolver $blockTitleStoreResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->blockTitleStoreResolver = $blockTitleStoreResolver;
        $this->blockTitleStoreValueFactory = $blockTitleStoreValueFactory;
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
                ->from($this->resourceConnection->getTableName('aw_autorelated_rule_block_title'))
                ->where('rule_id = :id');
            $blockTitleStoreValuesData = $connection->fetchAll($select, ['id' => $entityId]);

            $blockTitleStoreValues = [];
            foreach ($blockTitleStoreValuesData as $blockTitleStoreValue) {
                /** @var RuleTitleStoreValueInterface $blockTitleStoreValueEntity */
                $blockTitleStoreValueEntity = $this->blockTitleStoreValueFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $blockTitleStoreValueEntity,
                    $blockTitleStoreValue,
                    RuleTitleStoreValueInterface::class
                );
                $blockTitleStoreValues[] = $blockTitleStoreValueEntity;
            }

            $entity->setTitleStoreValues($blockTitleStoreValues);
            $storeId = isset($arguments['store_id']) ? $arguments['store_id'] : Store::DEFAULT_STORE_ID;

            if ($storeValue = $this->blockTitleStoreResolver->getValueByStoreId(
                $blockTitleStoreValues,
                $storeId
            )) {
                $entity->setTitle($storeValue);
            }
        }
        return $entity;
    }
}
