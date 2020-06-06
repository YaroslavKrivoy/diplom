<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\CustomerCardInterface;
use Magento\Framework\Model\AbstractModel;

class CustomerCard extends AbstractModel implements CustomerCardInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\GiftCard\Model\ResourceModel\CustomerCard::class);
        $this->setIdFieldName('customer_card_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerCardId()
    {
        return $this->_getData(CustomerCardInterface::CUSTOMER_CARD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerCardId($customerCardId)
    {
        $this->setData(CustomerCardInterface::CUSTOMER_CARD_ID, $customerCardId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountId()
    {
        return $this->_getData(CustomerCardInterface::ACCOUNT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountId($accountId)
    {
        $this->setData(CustomerCardInterface::ACCOUNT_ID, $accountId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->_getData(CustomerCardInterface::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        $this->setData(CustomerCardInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

}