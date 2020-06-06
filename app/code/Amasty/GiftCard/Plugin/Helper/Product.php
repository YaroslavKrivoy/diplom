<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Helper;

use Amasty\GiftCard\Model\Product\Type\GiftCard;

class Product
{
    const GIFT_PARAMS = [
        'am_giftcard_amount',
        'am_giftcard_amount_custom',
        'am_giftcard_image',
        'am_giftcard_sender_name',
        'am_giftcard_sender_email',
        'am_giftcard_recipient_name',
        'am_giftcard_recipient_email',
        'am_giftcard_date_delivery',
        'am_giftcard_date_delivery_timezone',
        'am_giftcard_message'
    ];

    /**
     * @param \Magento\Catalog\Helper\Product $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $buyRequest
     * @return array
     */
    public function beforePrepareProductOptions(
        \Magento\Catalog\Helper\Product $subject,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\DataObject $buyRequest
    ) {
        if ($product->getTypeId() === GiftCard::TYPE_GIFTCARD_PRODUCT) {
            $giftOptions = $buyRequest->getData();
            $options = [];
            foreach ($giftOptions as $giftOption => $giftValue) {
                if (in_array($giftOption, self::GIFT_PARAMS)) {
                    $options[$giftOption] = $giftValue;
                }
            }
            $buyRequest->setData('options', $options);
        }

        return [$product, $buyRequest];
    }
}
