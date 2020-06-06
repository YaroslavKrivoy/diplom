<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api;

interface GiftCardManagementInterface
{
    /**
     * Adds a gift card to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $giftCard The coupon code data.
     * @return bool
     */
    public function set($cartId, $giftCard);

    /**
     * Deletes a gift card from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $giftCard
     * @return bool
     */
    public function remove($cartId, $giftCard);
}
