<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Repository;

use Amasty\GiftCard\Api\Data\AccountInterface;
use Amasty\GiftCard\Api\AccountRepositoryInterface;
use Amasty\GiftCard\Model\AccountFactory;
use Amasty\GiftCard\Model\Code;
use Amasty\GiftCard\Model\Repository\CodeRepository;
use Amasty\GiftCard\Model\ResourceModel\Account;
use Amasty\GiftCard\Model\ResourceModel\Account\CollectionFactory;
use Amasty\GiftCard\Model\ResourceModel\Code\CollectionFactory as CodeFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * @var Account
     */
    private $accountResource;

    /**
     * @var array
     */
    private $accounts;

    /**
     * @var CollectionFactory
     */
    private $accountCollectionFactory;

    /**
     * @var CodeRepository
     */
    private $codeRepository;

    /**
     * @var CodeFactory
     */
    private $codeCollectionFactory;

    public function __construct(
        AccountFactory $accountFactory,
        Account $accountResource,
        CollectionFactory $accountCollectionFactory,
        CodeRepository $codeRepository,
        CodeFactory $codeCollectionFactory
    ) {
        $this->accountFactory = $accountFactory;
        $this->accountResource = $accountResource;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->codeRepository = $codeRepository;
        $this->codeCollectionFactory = $codeCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccountInterface $account)
    {
        try {
            $codeId = $account->getCodeId();
            if ($codeId) {
                $code = $this->codeRepository->getById($codeId);
                if ($code->getUsed() == Code::STATE_USED) {
                    throw new CouldNotSaveException(__('This code is used'));
                }
            } else {
                /** @var \Amasty\GiftCard\Model\ResourceModel\Code\Collection $codeCollection */
                $codeCollection = $this->codeCollectionFactory->create();

                /** @var \Amasty\GiftCard\Model\Code $code */
                $code = $codeCollection->addFieldToFilter('used', Code::STATE_UNUSED)
                    ->getFirstItem();
                $codeId = $code->getCodeId();
                $account->setCodeId($codeId);
            }
            $this->accountResource->save($account);
        } catch (\Exception $e) {
            if ($account->getAccountId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save account with ID %1. Error: %2',
                        [$account->getAccountId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new account. Error: %1', $e->getMessage()));
        }
        $code->setUsed(Code::STATE_USED);
        $this->codeRepository->save($code);

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function saveCurrent(AccountInterface $account)
    {
        try {
            $this->accountResource->save($account);
        } catch (\Exception $e) {
            if ($account->getAccountId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save account with ID %1. Error: %2',
                        [$account->getAccountId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new account. Error: %1', $e->getMessage()));
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($accountId)
    {
        if (!isset($this->accounts[$accountId])) {
            /** @var \Amasty\GiftCard\Model\Account $account */
            $account = $this->accountFactory->create();
            $this->accountResource->load($account, $accountId);
            if (!$account->getAccountId()) {
                throw new NoSuchEntityException(__('account with specified ID "%1" not found.', $accountId));
            }
            $this->accounts[$accountId] = $account;
        }

        return $this->accounts[$accountId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AccountInterface $account)
    {
        try {
            $this->accountResource->delete($account);
            unset($this->accounts[$account->getAccountId()]);
        } catch (\Exception $e) {
            if ($account->getAccountId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove account with ID %1. Error: %2',
                        [$account->getAccountId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove account. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($accountId)
    {
        $accountModel = $this->getById($accountId);
        $this->delete($accountModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        /** @var \Amasty\GiftCard\Model\ResourceModel\Account\Collection $accountCollection */
        $accountCollection = $this->accountCollectionFactory->create();

        return $accountCollection->getItems();
    }
}
