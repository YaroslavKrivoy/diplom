<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 15.11.19
 * Time: 10:18
 */

namespace KozakGroup\RewriteOrderEditor\Model\Checkout;


class ShippingInformationPlugin
{

    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    )
    {
        $this->quoteRepository = $quoteRepository;
    }


    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $extAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes();
        if ($extAttributes) {
            $pickupDate = $extAttributes->getPickupDate();
            $pickupTime = $extAttributes->getPickupTime();
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setPickupDate($pickupDate);
            $quote->setPickupTime($pickupTime);
        }
    }

}