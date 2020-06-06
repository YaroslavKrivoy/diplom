<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Repository;

use Amasty\GiftCard\Api\Data\QuoteInterface;
use Amasty\GiftCard\Api\QuoteRepositoryInterface;
use Amasty\GiftCard\Model\QuoteFactory;
use Amasty\GiftCard\Model\ResourceModel\Quote;
use Amasty\GiftCard\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class QuoteRepository implements QuoteRepositoryInterface
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var Quote
     */
    private $quoteResource;

    /**
     * @var array
     */
    private $quotes;

    /**
     * @var CollectionFactory
     */
    private $quoteCollectionFactory;

    public function __construct(
        QuoteFactory $quoteFactory,
        Quote $quoteResource,
        CollectionFactory $quoteCollectionFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->quoteResource = $quoteResource;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QuoteInterface $quote)
    {
        try {
            $this->quoteResource->save($quote);
        } catch (\Exception $e) {
            if ($quote->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save quote with ID %1. Error: %2',
                        [$quote->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new quote. Error: %1', $e->getMessage()));
        }

        return $quote;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($entityId)
    {
        if (!isset($this->quotes[$entityId])) {
            /** @var \Amasty\GiftCard\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
            $this->quoteResource->load($quote, $entityId);
            if (!$quote->getEntityId()) {
                throw new NoSuchEntityException(__('quote with specified ID "%1" not found.', $entityId));
            }
            $this->quotes[$entityId] = $quote;
        }

        return $this->quotes[$entityId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QuoteInterface $quote)
    {
        try {
            $this->quoteResource->delete($quote);
            unset($this->quotes[$quote->getEntityId()]);
        } catch (\Exception $e) {
            if ($quote->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove quote with ID %1. Error: %2',
                        [$quote->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove quote. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        $quoteModel = $this->getById($entityId);
        $this->delete($quoteModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        /** @var \Amasty\GiftCard\Model\ResourceModel\Quote\Collection $quoteCollection */
        $quoteCollection = $this->quoteCollectionFactory->create();

        return $quoteCollection->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getByQuoteId($quoteId)
    {
        /** @var \Amasty\GiftCard\Model\Code $codeModel */
        $quote = $this->quoteFactory->create();
        $this->quoteResource->load($quote, $quoteId, QuoteInterface::QUOTE_ID);

        if (!$quote->getQuoteId()) {
            throw new NoSuchEntityException(__('Entity with specified Quote Id "%1" not found.', $quoteId));
        }

        return $quote;
    }
}
