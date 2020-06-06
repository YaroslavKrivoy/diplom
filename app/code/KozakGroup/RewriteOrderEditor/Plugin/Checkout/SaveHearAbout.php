<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 11.11.19
 * Time: 9:32
 */

namespace KozakGroup\RewriteOrderEditor\Plugin\Checkout;


class SaveHearAbout
{
    protected $quote;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quote
    ) {
        $this->quote = $quote;
    }

    public function beforeSaveAddressInformation(\Magento\Checkout\Model\ShippingInformationManagement $subject, $cartId, \Magento\Checkout\Api\Data\ShippingInformationInterface $address
    ) {
        $quote = $this->quote->getActive($cartId);
        if(!$quote->getCustomer()->getId()){
            throw new \Magento\Framework\Exception\LocalizedException(__('Where did you hear about us is required.'));
        }
        $quote->setHearAboutId('');
    }
}