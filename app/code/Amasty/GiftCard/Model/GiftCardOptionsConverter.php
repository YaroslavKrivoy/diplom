<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\AmGiftCardOptionsInterface as AmGiftCardOptionsInterface;
use Amasty\GiftCard\Model\Options as Options;

class GiftCardOptionsConverter
{
    /**
     * @param AmGiftCardOptionsInterface $amgiftcardOptions
     * @return array
     */
    public function prepareToBuyRequest($amgiftcardOptions)
    {
        $requestData = [];
        $requestData[AmGiftCardOptionsInterface::CARD_AMOUNT] = $amgiftcardOptions->getCardAmount();
        $requestData[AmGiftCardOptionsInterface::SENDER_EMAIL] = $amgiftcardOptions->getSenderEmail();
        $requestData[AmGiftCardOptionsInterface::RECIPIENT_EMAIL] = $amgiftcardOptions->getRecipientEmail();
        $requestData[AmGiftCardOptionsInterface::IMAGE] = $amgiftcardOptions->getImage();
        if ($amgiftcardOptions->getDeliveryTimezone()) {
            $requestData[AmGiftCardOptionsInterface::DELIVERY_TIMEZONE] = $amgiftcardOptions->getDeliveryTimezone();
        }
        if ($amgiftcardOptions->getCardValue()) {
            $requestData[AmGiftCardOptionsInterface::CARD_VALUE] = $amgiftcardOptions->getCardValue();
        }
        if ($amgiftcardOptions->getRecipientName()) {
            $requestData[AmGiftCardOptionsInterface::RECIPIENT_NAME] = $amgiftcardOptions->getRecipientName();
        }
        if ($amgiftcardOptions->getSenderName()) {
            $requestData[AmGiftCardOptionsInterface::SENDER_NAME] = $amgiftcardOptions->getSenderName();
        }
        if ($amgiftcardOptions->getMessage()) {
            $requestData[AmGiftCardOptionsInterface::MESSAGE] = $amgiftcardOptions->getMessage();
        }
        if ($amgiftcardOptions->getDeliveryDate()) {
            $requestData[AmGiftCardOptionsInterface::DELIVERY_DATE] = $amgiftcardOptions->getDeliveryDate();
        }

        return $requestData;
    }

    /**
     * @param array $requestData
     * @param Options $productOption
     * @return void
     */
    public function prepareToProductOption($requestData, $productOption)
    {
        foreach ($requestData as $key => $value) {
            switch ($key) {
                case Options::CARD_AMOUNT:
                    $productOption->setCardAmount($value);
                    break;
                case Options::CARD_VALUE:
                    $productOption->setCardValue($value);
                    break;
                case Options::MESSAGE:
                    $productOption->setMessage($value);
                    break;
                case Options::DELIVERY_TIMEZONE:
                    $productOption->setDeliveryTimezone($value);
                    break;
                case Options::DELIVERY_DATE:
                    $productOption->setDeliveryDate($value);
                    break;
                case Options::RECIPIENT_EMAIL:
                    $productOption->setRecipientEmail($value);
                    break;
                case Options::RECIPIENT_NAME:
                    $productOption->setRecipientName($value);
                    break;
                case Options::SENDER_EMAIL:
                    $productOption->setSenderEmail($value);
                    break;
                case Options::SENDER_NAME:
                    $productOption->setSenderName($value);
                    break;
                case Options::IMAGE:
                    $productOption->setImage($value);
            }
        }
    }
}
