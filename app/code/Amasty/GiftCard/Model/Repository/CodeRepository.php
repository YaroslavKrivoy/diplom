<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Repository;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Api\CodeRepositoryInterface;
use Amasty\GiftCard\Model\CodeFactory;
use Amasty\GiftCard\Model\ResourceModel\Code;
use Amasty\GiftCard\Model\ResourceModel\Code\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class CodeRepository implements CodeRepositoryInterface
{
    /**
     * @var CodeFactory
     */
    private $codeFactory;

    /**
     * @var Code
     */
    private $codeResource;

    /**
     * @var array
     */
    private $codes;

    /**
     * @var CollectionFactory
     */
    private $codeCollectionFactory;

    public function __construct(
        CodeFactory $codeFactory,
        Code $codeResource,
        CollectionFactory $codeCollectionFactory
    ) {
        $this->codeFactory = $codeFactory;
        $this->codeResource = $codeResource;
        $this->codeCollectionFactory = $codeCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CodeInterface $code)
    {
        try {
            $this->codeResource->save($code);
        } catch (\Exception $e) {
            if ($code->getCodeId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save code with ID %1. Error: %2',
                        [$code->getCodeId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new code. Error: %1', $e->getMessage()));
        }

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($codeId)
    {
        if (!isset($this->codes[$codeId])) {
            /** @var \Amasty\GiftCard\Model\Code $code */
            $code = $this->codeFactory->create();
            $this->codeResource->load($code, $codeId);
            if (!$code->getCodeId()) {
                throw new NoSuchEntityException(__('code with specified ID "%1" not found.', $codeId));
            }
            $this->codes[$codeId] = $code;
        }

        return $this->codes[$codeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdByCode($giftCode)
    {
        if (!isset($this->codes[$giftCode])) {
            /** @var \Amasty\GiftCard\Model\Code $code */
            $code = $this->codeFactory->create();
            $this->codeResource->load($code, $giftCode, 'code');
            if (!$code->getCodeId()) {
                throw new NoSuchEntityException(__('Coupon with specified code  "%1" not found.', $giftCode));
            }
            $this->codes[$giftCode] = $code->getCodeId();
        }

        return $this->codes[$giftCode];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CodeInterface $code)
    {
        try {
            $this->codeResource->delete($code);
            unset($this->codes[$code->getCodeId()]);
        } catch (\Exception $e) {
            if ($code->getCodeId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove code with ID %1. Error: %2',
                        [$code->getCodeId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove code. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($codeId)
    {
        $codeModel = $this->getById($codeId);
        $this->delete($codeModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        /** @var \Amasty\GiftCard\Model\ResourceModel\Code\Collection $codeCollection */
        $codeCollection = $this->codeCollectionFactory->create();

        return $codeCollection->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code)
    {
        /** @var \Amasty\GiftCard\Model\Code $codeModel */
        $codeModel = $this->codeFactory->create();
        $this->codeResource->load($codeModel, $code, CodeInterface::CODE);

        if (!$codeModel->getCode()) {
            throw new NoSuchEntityException(__('Entity with specified Code "%1" not found.', $code));
        }

        return $codeModel;
    }
}
