<?php

namespace Aitoc\DimensionalShipping\Model\Sales\Model\Order\Pdf;

use Aitoc\DimensionalShipping\Helper\Data as DimensionalShippingHelper;
use Aitoc\DimensionalShipping\Model\ResourceModel\OrderBox\CollectionFactory as OrderBoxCollectionFactory;
use Aitoc\DimensionalShipping\Model\ResourceModel\OrderItemBox\CollectionFactory as OrderItemBoxCollectionFactory;
use Magento\Sales\Model\Order\Pdf\Config;

class Shipment extends \Magento\Sales\Model\Order\Pdf\Shipment
{

    protected $orderBoxCollectionFactory;
    protected $orderItemBoxCollectionFactory;
    protected $helper;
    protected $itemRepository;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        OrderBoxCollectionFactory $orderBoxCollectionFactory,
        OrderItemBoxCollectionFactory $orderItemBoxCollectionFactory,
        DimensionalShippingHelper $helper,
        \Magento\Sales\Model\Order\ItemRepository $itemRepository,
        array $data = []
    ) {
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
        $this->orderBoxCollectionFactory     = $orderBoxCollectionFactory;
        $this->orderItemBoxCollectionFactory = $orderItemBoxCollectionFactory;
        $this->helper                        = $helper;
        $this->itemRepository                = $itemRepository;
    }

    public function getPdf($shipments = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                $this->_localeResolver->emulate($shipment->getStoreId());
                $this->_storeManager->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Packing Slip # ') . $shipment->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            $this->_drowBoxInfo($page, $order, $shipment, $pdf);
        }

        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            $this->_localeResolver->revert();
        }

        return $pdf;
    }

    protected function _drowBoxInfo(\Zend_Pdf_Page $page, $order, $shipment, &$pdf)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));


        $page->drawText(
            __('Order Boxes: '),
            35,
            $this->y - 9,
            'UTF-8'
        );
        $this->y -= 18;

        $orderBoxCollection = $this->orderBoxCollectionFactory->create()
            ->addFieldToFilter('order_id', $order->getId())
            ->getItems();
        foreach ($orderBoxCollection as $orderBox) {
            $box = $this->helper->getBoxById($orderBox->getBoxId());
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->setLineWidth(0.5);
            $page->drawText(
                __('Box: ') . $box->getName(),
                35,
                $this->y -= 8,
                'UTF-8'
            );
            $this->y -= 5;

            /* Add table head */
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y - 15);
            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

            //columns headers
            $lines[0][] = ['text' => __('Products'), 'feed' => 35];

            $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

            $lines[0][] = ['text' => __('QTY'), 'feed' => 365, 'align' => 'right'];

            $lineBlock = ['lines' => $lines, 'height' => 5];

            $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);

            $orderItemBoxCollection = $this->orderItemBoxCollectionFactory->create()
                ->addFieldToFilter('order_id', $order->getId())
                ->addFieldToFilter('order_box_id', $orderBox->getId())
                ->addGroupByNameField('sku')
                ->getItems();

            $this->y -= 30;
            foreach ($orderItemBoxCollection as $orderBoxItem) {
                $items = $order->getAllItems();
                $item  = null;

                foreach ($shipment->getAllItems() as $orderItem) {
                    if ($orderItem->getOrderItem()->getParentItem()) {
                        continue;
                    }
                }
                $item = $this->itemRepository->get($orderBoxItem->getOrderItemId());
                $orderItemBoxCollectionCount = $this->orderItemBoxCollectionFactory->create()
                    ->addFieldToFilter('order_id', $order->getId())
                    ->addFieldToFilter('order_box_id', $orderBox->getId())
                    ->addFieldToFilter('sku', $item->getSku())
                    ->count();

                // draw Product name
                $lines2[0]   = [['text' => $this->string->split($item->getName(), 35, true, true), 'feed' => 35]];
                $lines2[0][] = [
                    'text'  => $this->string->split($item->getSku(), 17),
                    'feed'  => 290,
                    'align' => 'right',
                ];
                $lines2[0][] = [
                    'text'  => $this->string->split($orderItemBoxCollectionCount, 17),
                    'feed'  => 365,
                    'align' => 'right',
                ];

                $lineBlock2 = ['lines' => $lines2, 'height' => 20];

                $this->drawLineBlocks($page, [$lineBlock2], ['table_header' => true]);
                //$page = end($pdf->pages);
            }
        }


        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
    }
}
