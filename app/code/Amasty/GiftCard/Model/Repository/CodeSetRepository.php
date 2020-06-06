<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Repository;

use Amasty\GiftCard\Api\Data\CodeSetInterface;
use Amasty\GiftCard\Api\CodeSetRepositoryInterface;
use Amasty\GiftCard\Model\CodeSetFactory;
use Amasty\GiftCard\Model\ResourceModel\CodeSet;
use Amasty\GiftCard\Model\ResourceModel\CodeSet\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class CodeSetRepository implements CodeSetRepositoryInterface
{
    /**
     * @var CodeSetFactory
     */
    private $codeSetFactory;

    /**
     * @var CodeSet
     */
    private $codeSetResource;

    /**
     * @var array
     */
    private $codeSets;

    /**
     * @var CollectionFactory
     */
    private $codeSetCollectionFactory;

    public function __construct(
        CodeSetFactory $codeSetFactory,
        CodeSet $codeSetResource,
        CollectionFactory $codeSetCollectionFactory
    ) {
        $this->codeSetFactory = $codeSetFactory;
        $this->codeSetResource = $codeSetResource;
        $this->codeSetCollectionFactory = $codeSetCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CodeSetInterface $codeSet)
    {
        try {
            $this->codeSetResource->save($codeSet);
        } catch (\Exception $e) {
            if ($codeSet->getCodeSetId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save codeSet with ID %1. Error: %2',
                        [$codeSet->getCodeSetId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new codeSet. Error: %1', $e->getMessage()));
        }

        return $codeSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($codeSetId)
    {
        if (!isset($this->codeSets[$codeSetId])) {
            /** @var \Amasty\GiftCard\Model\CodeSet $codeSet */
            $codeSet = $this->codeSetFactory->create();
            $this->codeSetResource->load($codeSet, $codeSetId);
            if (!$codeSet->getCodeSetId()) {
                throw new NoSuchEntityException(__('codeSet with specified ID "%1" not found.', $codeSetId));
            }
            $this->codeSets[$codeSetId] = $codeSet;
        }

        return $this->codeSets[$codeSetId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CodeSetInterface $codeSet)
    {
        try {
            $this->codeSetResource->delete($codeSet);
            unset($this->codeSets[$codeSet->getCodeSetId()]);
        } catch (\Exception $e) {
            if ($codeSet->getCodeSetId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove codeSet with ID %1. Error: %2',
                        [$codeSet->getCodeSetId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove codeSet. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($codeSetId)
    {
        $codeSetModel = $this->getById($codeSetId);
        $this->delete($codeSetModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        /** @var \Amasty\GiftCard\Model\ResourceModel\CodeSet\Collection $codeSetCollection */
        $codeSetCollection = $this->codeSetCollectionFactory->create();

        return $codeSetCollection->getItems();
    }
}
