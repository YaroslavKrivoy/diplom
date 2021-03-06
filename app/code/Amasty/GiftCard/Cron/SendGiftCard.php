<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Cron;

class SendGiftCard
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Account\Collection
     */
    protected $accountCollection;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Amasty\GiftCard\Model\ResourceModel\Account\CollectionFactory $accountCollection
    ) {

        $this->date = $date;
        $this->accountCollection = $accountCollection;
    }

    public function execute()
    {
        $currentDate = $this->date->gmtDate('Y-m-d H:i:s');
        /** @var \Amasty\GiftCard\Model\ResourceModel\Account\Collection $collection */
        $collection = $this->accountCollection->create()
            ->addFieldToFilter('date_delivery', ['lteq' => $currentDate])
            ->addFieldToFilter('is_sent', 0);

        $collection->walk('sendDataToMail');

        return $this;
    }
}
