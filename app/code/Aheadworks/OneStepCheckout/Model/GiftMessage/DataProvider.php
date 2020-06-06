<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Model\GiftMessage;

use Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterface;
use Aheadworks\OneStepCheckout\Api\Data\GiftMessageConfigInterfaceFactory;
use Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterface;
use Aheadworks\OneStepCheckout\Api\Data\GiftMessageInterfaceFactory;
use Aheadworks\OneStepCheckout\Api\Data\GiftMessageSectionInterface;
use Aheadworks\OneStepCheckout\Api\Data\GiftMessageSectionInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GiftMessage\Api\CartRepositoryInterface as GiftMessageCartRepositoryInterface;
use Magento\GiftMessage\Api\ItemRepositoryInterface as GiftMessageItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Boolean;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class DataProvider
 * @package Aheadworks\OneStepCheckout\Model\GiftMessage
 */
class DataProvider
{
    /**
     * @var GiftMessageConfigInterfaceFactory
     */
    private $giftMessageConfigFactory;

    /**
     * @var GiftMessageInterfaceFactory
     */
    private $giftMessageFactory;

    /**
     * @var GiftMessageSectionInterfaceFactory
     */
    private $giftMessageSectionFactory;

    /**
     * @var GiftMessageCartRepositoryInterface
     */
    private $giftMessageCartRepository;

    /**
     * @var GiftMessageItemRepositoryInterface
     */
    private $giftMessageItemRepository;

    /**
     * @var Config
     */
    private $giftMessageConfig;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param GiftMessageConfigInterfaceFactory $giftMessageConfigFactory
     * @param GiftMessageInterfaceFactory $giftMessageFactory
     * @param GiftMessageSectionInterfaceFactory $giftMessageSectionFactory
     * @param GiftMessageCartRepositoryInterface $giftMessageCartRepository
     * @param GiftMessageItemRepositoryInterface $giftMessageItemRepository
     * @param Config $giftMessageConfig
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        GiftMessageConfigInterfaceFactory $giftMessageConfigFactory,
        GiftMessageInterfaceFactory $giftMessageFactory,
        GiftMessageSectionInterfaceFactory $giftMessageSectionFactory,
        GiftMessageCartRepositoryInterface $giftMessageCartRepository,
        GiftMessageItemRepositoryInterface $giftMessageItemRepository,
        Config $giftMessageConfig,
        CartRepositoryInterface $cartRepository
    ) {
        $this->giftMessageConfigFactory = $giftMessageConfigFactory;
        $this->giftMessageFactory = $giftMessageFactory;
        $this->giftMessageSectionFactory = $giftMessageSectionFactory;
        $this->giftMessageCartRepository = $giftMessageCartRepository;
        $this->giftMessageItemRepository = $giftMessageItemRepository;
        $this->giftMessageConfig = $giftMessageConfig;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Retrieve gift message data
     *
     * @param int $cartId
     * @return GiftMessageSectionInterface
     */
    public function getData($cartId)
    {
        /** @var GiftMessageSectionInterface $giftMessageSection */
        $giftMessageSection = $this->giftMessageSectionFactory->create();
        try {
            $cart = $this->cartRepository->getActive($cartId);
            $storeId = $cart->getStoreId();
            $isOrderLevelEnabled = $this->giftMessageConfig->isOrderMessageAllowed($storeId);
            $isItemsLevelEnabled = $this->giftMessageConfig->isItemsMessageAllowed($storeId);
            if ($isOrderLevelEnabled) {
                $giftMessageSection->setOrderMessage($this->getOrderMessage($cart, $isOrderLevelEnabled));
            }
            if ($isItemsLevelEnabled) {
                $giftMessageSection->setItemMessages($this->getItemsMessage($cart, $isItemsLevelEnabled));
            }
        } catch (NoSuchEntityException $e) {
        }

        return $giftMessageSection;
    }

    /**
     * Retrieve order message
     *
     * @param CartInterface|Quote $cart
     * @param bool $isOrderLevelEnabled
     * @return GiftMessageInterface|null
     */
    private function getOrderMessage($cart, $isOrderLevelEnabled)
    {
        $orderMessage = $this->giftMessageCartRepository->get($cart->getId());
        $isOrderLevelEnabled = $cart->isVirtual() ? false : $isOrderLevelEnabled;
        /** @var GiftMessageConfigInterface $giftConfigMessage */
        $giftConfigMessage = $this->giftMessageConfigFactory->create();
        $giftConfigMessage->setIsEnabled($isOrderLevelEnabled);
        /** @var GiftMessageInterface $giftMessage */
        $giftMessage = $this->giftMessageFactory->create();
        $giftMessage
            ->setConfig($giftConfigMessage)
            ->setMessage($orderMessage);

        return $giftMessage;
    }

    /**
     * Retrieve items messages
     *
     * @param CartInterface|Quote $cart
     * @param bool $isItemsLevelEnabled
     * @return GiftMessageInterface[]|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getItemsMessage($cart, $isItemsLevelEnabled)
    {
        $itemMessages = [];
        foreach ($cart->getAllVisibleItems() as $item) {
            $itemId = $item->getId();
            $isAvailable = $item->getProduct()->getGiftMessageAvailable();

            /** @var GiftMessageConfigInterface $giftConfigMessage */
            $giftConfigMessage = $this->giftMessageConfigFactory->create();
            /** @var GiftMessageInterface $giftMessage */
            $giftMessage = $this->giftMessageFactory->create();
            if ($isAvailable !== null && $isAvailable != Boolean::VALUE_USE_CONFIG) {
                $isItemsLevelEnabled = (bool)$isAvailable;
            }
            $message = $this->giftMessageItemRepository->get($cart->getId(), $itemId);
            $giftConfigMessage
                ->setIsEnabled($isItemsLevelEnabled)
                ->setItemId($itemId);
            $giftMessage
                ->setConfig($giftConfigMessage)
                ->setMessage($message);
            $itemMessages[] = $giftMessage;
        }
        return count($itemMessages) === 0 ? null : $itemMessages;
    }
}
