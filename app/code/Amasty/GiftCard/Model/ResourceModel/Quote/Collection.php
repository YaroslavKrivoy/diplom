<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Model\ResourceModel\Quote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var $this
     */
    private $giftCards;

    /**
     * @var $this
     */
    private $giftCardsWithAccount;

    protected function _construct()
    {
        $this->_init('Amasty\GiftCard\Model\Quote', 'Amasty\GiftCard\Model\ResourceModel\Quote');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function joinAccount()
    {
        $this->getSelect()->join(
            ['amgiftcard_account' => $this->getTable('amasty_amgiftcard_account')],
            'amgiftcard_account.account_id = main_table.account_id'
        )->join(
            ['amgiftcard_code' => $this->getTable('amasty_amgiftcard_code')],
            'amgiftcard_code.code_id = amgiftcard_account.code_id'
        );

        return $this;
    }

    public function joinOrder()
    {
        $this->getSelect()
            ->join(
                ['order' => $this->getTable('sales_order')],
                'order.quote_id = main_table.quote_id',
                []
            )
            ->join(
                ['order_grid' => $this->getTable('sales_order_grid')],
                'order_grid.entity_id = order.entity_id',
                ['order_id' => 'entity_id', 'created_at', 'billing_name', 'shipping_name', 'grand_total', 'increment_id', 'store_id']
            );

        return $this;
    }

    /**
     * @param $quoteId
     * @return $this
     */
    public function getGiftCardsByQuoteId($quoteId)
    {
        if (!$this->giftCards) {
            $this->giftCards = $this->addFieldToFilter('quote_id', ['eq' => $quoteId]);
        }

        return $this->giftCards;
    }

    /**
     * @param $quoteId
     * @return $this
     */
    public function getGiftCardsWithAccount($quoteId)
    {
        if (!$this->giftCardsWithAccount) {
            $this->giftCardsWithAccount = $this->getGiftCardsByQuoteId($quoteId)->joinAccount();
        }

        return $this->giftCardsWithAccount;
    }

    /**
     * @param array|string $objMethod
     * @param array $args
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function each($objMethod, $args = [])
    {
        if ($objMethod instanceof \Closure) {
            foreach ($this->getItems() as $item) {
                $objMethod($item, ...$args);
            }
        } elseif (is_array($objMethod)) {
            foreach ($this->getItems() as $item) {
                call_user_func($objMethod, $item, ...$args);
            }
        } else {
            foreach ($this->getItems() as $item) {
                $item->$objMethod(...$args);
            }
        }
    }
}
