<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Observer;

use Amasty\GiftCard\Model\Product\Type\GiftCard;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class GenerateGiftCardAccount implements ObserverInterface
{

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\Collection
     */
    protected $invoiceCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice
     */
    protected $invoiceResourceModel;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\GiftCard\Model\Account
     */
    protected $account;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Sales\Model\Order\ItemRepository
     */
    private $itemRepository;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var \Amasty\GiftCard\Model\Config
     */
    private $config;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\Collection $invoiceCollection,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order\InvoiceFactory $invoice,
        \Magento\Sales\Model\ResourceModel\Order\Invoice $invoiceResourceModel,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\GiftCard\Model\Account $account,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\Order\ItemRepository $itemRepository,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\Store $store,
        \Amasty\GiftCard\Model\Config $config
    ) {
        $this->invoiceCollection = $invoiceCollection;
        $this->coreRegistry = $coreRegistry;
        $this->invoice = $invoice;
        $this->invoiceResourceModel = $invoiceResourceModel;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->account = $account;
        $this->messageManager = $messageManager;
        $this->itemRepository = $itemRepository;
        $this->escaper = $escaper;
        $this->store = $store;
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getInvoice();

        $customerId = !$this->config->getAllowThemselves() ? $order->getCustomerId() : null;

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() != GiftCard::TYPE_GIFTCARD_PRODUCT) {
                continue;
            }
            if (!$invoice) {
                $cloneInvoiceCollection = clone $this->invoiceCollection;
                $cloneInvoiceCollection->getSelect()->reset('where');
                $cloneInvoiceCollection->clear();
                $invoice = $cloneInvoiceCollection->addFieldToFilter('order_item_id', $item->getId())
                    ->load();
            }
            $options = $item->getProductOptions();
            if (isset($options['am_giftcard_created_codes']) && !empty($options['am_giftcard_created_codes'])) {
                continue;
            }

            $this->generateAccount($invoice, $order, $item, $options, $customerId);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param $order
     * @param $item
     * @param $options
     * @param null $customerId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generateAccount($invoice, $order, $item, $options, $customerId = null)
    {
        $loadedInvoices = [];
        $qty = 0;

        $paidInvoiceItems = (isset($options['am_giftcard_paid_invoice_items'])
            ? $options['am_giftcard_paid_invoice_items']
            : []);
        foreach ($invoice->getItems() as $invoiceItem) {
            $invoiceId = $invoiceItem->getParentId();
            if (isset($loadedInvoices[$invoiceId])) {
                $invoice = $loadedInvoices[$invoiceId];
            } elseif ($invoice instanceof \Magento\Sales\Model\ResourceModel\Order\Invoice\Item\Collection) {
                $invoice = $this->invoice->create();
                $this->invoiceResourceModel->load($invoice, $invoiceId);
                $loadedInvoices[$invoiceId] = $invoice;
            } else {
                $loadedInvoices[$invoiceId] = $invoice;
            }

            if ($invoice->getState() == Invoice::STATE_PAID
                && !in_array($invoiceItem->getId(), $paidInvoiceItems)
            ) {
                $qty += $invoiceItem->getQty();
                $paidInvoiceItems[] = $invoiceItem->getId();
            }
        }
        $options['am_giftcard_paid_invoice_items'] = $paidInvoiceItems;
        $this->coreRegistry->register('am_giftcard_paid_invoice_items', $paidInvoiceItems, true);

        if ($qty > 0) {
            $customAmount = $this->prepareCustomOption($item, 'am_giftcard_amount_custom');
            $amount = $this->prepareCustomOption($item, 'am_giftcard_amount');
            $value = $this->dataHelper->getGiftAmount($customAmount, $amount);

            $lifetime = $this->dataHelper->getValueOrConfig(
                $item->getProduct()->getAmGiftcardLifetime(),
                'amgiftcard/card/lifetime'
            );

            $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
            $dataObject  = new \Magento\Framework\DataObject();
            $dataObject->setWebsiteId($websiteId)
                ->setAmount($value / $order->getBaseToOrderRate())
                ->setOrder($order)
                ->setLifetime($lifetime)
                ->setProductOptions($options)
                ->setOrderItem($item)
                ->setCustomerId($customerId);
            $listGoodAccounts = [];
            $codes = (isset($options['am_giftcard_created_codes']) ? $options['am_giftcard_created_codes'] : []);
            for ($i = 0; $i < $qty; $i++) {
                try {
                    $account = $this->account->createAccount($dataObject);
                    $listGoodAccounts[] = $account;
                    $codes[] = $account->getCode();
                } catch (LocalizedException $e) {
                    $codes[] = null;
                    $this->messageManager->addErrorMessage(
                        __("%1 Only %2 accounts were created.", $e->getMessage(), $i)
                    );
                    break;
                }
            }
            $options['am_giftcard_created_codes'] = $codes;

            $item->setProductOptions($options);
            $this->itemRepository->save($item);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @param $code
     * @return bool
     */
    private function prepareCustomOption($item, $code)
    {
        if ($option = $item->getProductOptionByCode($code)) {
            return $this->escaper->escapeHtml($option);
        }

        return false;
    }
}
