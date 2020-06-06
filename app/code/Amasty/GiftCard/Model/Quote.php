<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\QuoteInterface;

class Quote extends \Magento\Framework\Model\AbstractModel implements QuoteInterface
{
    const STATE_USED = 1;
    const STATE_UNUSED = 0;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    private $helperData;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\GiftCard\Helper\Data $helperData,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->helperData = $helperData;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\GiftCard\Model\ResourceModel\Quote');
        $this->setIdFieldName('entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->_getData(QuoteInterface::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        $this->setData(QuoteInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteId()
    {
        return $this->_getData(QuoteInterface::QUOTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(QuoteInterface::QUOTE_ID, $quoteId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeId()
    {
        return $this->_getData(QuoteInterface::CODE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeId($codeId)
    {
        $this->setData(QuoteInterface::CODE_ID, $codeId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountId()
    {
        return $this->_getData(QuoteInterface::ACCOUNT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountId($accountId)
    {
        $this->setData(QuoteInterface::ACCOUNT_ID, $accountId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftAmount()
    {
        return $this->_getData(QuoteInterface::GIFT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftAmount($giftAmount)
    {
        $this->setData(QuoteInterface::GIFT_AMOUNT, $giftAmount);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseGiftAmount()
    {
        return $this->_getData(QuoteInterface::BASE_GIFT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseGiftAmount($baseGiftAmount)
    {
        $this->setData(QuoteInterface::BASE_GIFT_AMOUNT, $baseGiftAmount);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_getData(QuoteInterface::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->setData(QuoteInterface::CODE, $code);

        return $this;
    }

    /**
     * @param $quote
     *
     * @return int
     */
    public function getSubtotal($quote)
    {
        $subtotal = $this->helperData->isAllowedToPaidForTax()
            ? $quote->getSubtotalInclTax()
            : $quote->getSubtotal();
        $subtotal += $this->helperData->isAllowedToPaidForShipping() ? $quote->getShippingInclTax() : 0;

        return $subtotal;
    }

    /**
     * @param $quote
     *
     * @return int
     */
    public function getBaseSubtotal($quote)
    {
        $baseSubtotal = $this->helperData->isAllowedToPaidForTax()
            ? $quote->getBaseSubtotalInclTax()
            : $quote->getBaseSubtotal();
        $baseSubtotal += $this->helperData->isAllowedToPaidForShipping() ? $quote->getBaseShippingInclTax() : 0;

        return $baseSubtotal;
    }
}
