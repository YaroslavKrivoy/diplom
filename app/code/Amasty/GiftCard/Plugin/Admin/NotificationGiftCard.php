<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Plugin\Admin;

use Amasty\GiftCard\Model\CodeFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Store\Model\Store;

class NotificationGiftCard
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CodeFactory
     */
    private $codeFactory;
    /**
     * @var Store
     */
    private $store;

    public function __construct(
        ManagerInterface $messageManage,
        Registry $registry,
        CodeFactory $codeFactory,
        Store $store
    ) {
        $this->messageManager = $messageManage;
        $this->registry       = $registry;
        $this->codeFactory    = $codeFactory;
        $this->store = $store;
    }

    /**
     * check free codes before invoice
     * notify user if count of free codes less than count of items
     * @param View $subject
     * @param \Closure $proceed
     *
     * @return mixed
     */
    public function aroundGetOrder(View $subject, \Closure $proceed)
    {
        if (!$this->registry->registry('amasty_check_giftcodes')) {
            $code = $this->codeFactory->create();
            $result = $proceed($subject);
            $arrayWithCodes = [];

            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($result->getAllVisibleItems() as $item) {
                if ($item->getProductType() === \Amasty\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD_PRODUCT) {
                    $qty = $item->getQtyOrdered();
                    $productOptions = $item->getProductOptions();
                    $codeSet = isset($productOptions['am_giftcard_code_set'])
                        ? $productOptions['am_giftcard_code_set']
                        : '';
                    $codesCount = $code->getCollection()->countOfFreeCodesByCodeSet($codeSet);
                    if (isset($arrayWithCodes[$codeSet])) {
                        $arrayWithCodes[$codeSet]['needle_codes'] += $qty;
                    } else {
                        $arrayWithCodes +=  [
                            $codeSet =>
                                [
                                    'available_codes' => $codesCount,
                                    'needle_codes'    => $qty
                                ]
                        ];
                    }
                }
            }
            foreach ($arrayWithCodes as $codeSetId => $codeSet) {
                if ($codeSet['available_codes'] < $codeSet['needle_codes']) {
                    $arrayWithCodesSet[] = $codeSetId;
                }
            }

            if (!empty($arrayWithCodesSet) && !$result->hasInvoices()) {
                $stringWithCodeSet = implode(", ", $arrayWithCodesSet);
                $this->messageManager->addWarningMessage(
                    __('Not enough free gift card codes in the code pools with id %1.'
                    . ' Please generate more codes before invoicing the order.', $stringWithCodeSet)
                );
            }
            $this->registry->register('amasty_check_giftcodes', true);

            return $result;
        }

        return $proceed($subject);
    }
}
