<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Cron;

class NotifyExpiredCards
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Account\Collection
     */
    protected $accountCollection;

    /**
     * @var \Amasty\GiftCard\Model\ConfigModel
     */
    private $configModel;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Amasty\GiftCard\Model\ResourceModel\Account\CollectionFactory $accountCollection,
        \Amasty\GiftCard\Model\ConfigModel $configModel
    ) {
        $this->date = $date;
        $this->accountCollection = $accountCollection;
        $this->configModel = $configModel;
    }

    public function execute()
    {
        if (!$this->configModel->getScopeValue('card/notify_expires_date')) {
            return $this;
        }
        $days = $this->configModel->getScopeValue('card/notify_expires_date_days');

        $date = $this->date->gmtDate('Y-m-d', "+{$days} days");
        $dateExpired = [
            'from' => $date." 00:00:00",
            'to'   => $date." 23:59:59",
        ];
        $collection = $this->accountCollection->create()
            ->addFieldToFilter('expired_date', $dateExpired);
        $collection->walk('sendExpiryNotification');

        return $this;
    }
}
