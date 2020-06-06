<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Repository;

use Amasty\GiftCard\Api\Data\CustomerCardInterface;
use Amasty\GiftCard\Api\CustomerCardRepositoryInterface;
use Amasty\GiftCard\Model\CustomerCardFactory;
use Amasty\GiftCard\Model\ResourceModel\CustomerCard;
use Amasty\GiftCard\Model\ResourceModel\CustomerCard\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class CustomerCardRepository implements CustomerCardRepositoryInterface
{
    /**
     * @var CustomerCardFactory
     */
    private $customerCardFactory;

    /**
     * @var CustomerCard
     */
    private $customerCardResource;

    /**
     * @var array
     */
    private $customerCards;

    /**
     * @var CollectionFactory
     */
    private $customerCardCollectionFactory;

    public function __construct(
        CustomerCardFactory $customerCardFactory,
        CustomerCard $customerCardResource,
        CollectionFactory $customerCardCollectionFactory
    ) {
        $this->customerCardFactory = $customerCardFactory;
        $this->customerCardResource = $customerCardResource;
        $this->customerCardCollectionFactory = $customerCardCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CustomerCardInterface $customerCard)
    {
        try {
            $this->customerCardResource->save($customerCard);
        } catch (\Exception $e) {
            if ($customerCard->getCustomerCardId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save customerCard with ID %1. Error: %2',
                        [$customerCard->getCustomerCardId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new customerCard. Error: %1', $e->getMessage()));
        }

        return $customerCard;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customerCardId)
    {
        if (!isset($this->customerCards[$customerCardId])) {
            /** @var \Amasty\GiftCard\Model\CustomerCard $customerCard */
            $customerCard = $this->customerCardFactory->create();
            $this->customerCardResource->load($customerCard, $customerCardId);
            if (!$customerCard->getCustomerCardId()) {
                throw new NoSuchEntityException(__('customerCard with specified ID "%1" not found.', $customerCardId));
            }
            $this->customerCards[$customerCardId] = $customerCard;
        }

        return $this->customerCards[$customerCardId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CustomerCardInterface $customerCard)
    {
        try {
            $this->customerCardResource->delete($customerCard);
            unset($this->customerCards[$customerCard->getCustomerCardId()]);
        } catch (\Exception $e) {
            if ($customerCard->getCustomerCardId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove customerCard with ID %1. Error: %2',
                        [$customerCard->getCustomerCardId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove customerCard. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customerCardId)
    {
        $customerCardModel = $this->getById($customerCardId);
        $this->delete($customerCardModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        /** @var \Amasty\GiftCard\Model\ResourceModel\CustomerCard\Collection $customerCardCollection */
        $customerCardCollection = $this->customerCardCollectionFactory->create();

        return $customerCardCollection->getItems();
    }
}
