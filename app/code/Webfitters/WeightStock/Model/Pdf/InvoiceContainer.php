<?php
namespace Webfitters\WeightStock\Model\Pdf;

class InvoiceContainer extends \Magento\Sales\Model\Order\Pdf\Invoice {

	protected $group;
    protected $product;
    protected $helper;

	public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Customer\Api\GroupRepositoryInterface $group,
        \Magento\Catalog\Model\Product $product,
        \Webfitters\WeightStock\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
    	$this->group = $group;
        $this->product = $product;
        $this->helper = $helper;
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
    }

    public function insertDocumentNumber(\Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }

	protected function _drawHeader(\Zend_Pdf_Page $page) {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 250, 'align' => 'right'];

        $lines[0][] = ['text' => __('Qty/Cases'), 'feed' => 355, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price/lb'), 'feed' => 445, 'align' => 'right'];

        $lines[0][] = ['text' => __('Weight'), 'feed' => 400, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price'), 'feed' => 305, 'align' => 'right'];

        $lines[0][] = ['text' => __('Unit Wgt.'), 'feed' => 495, 'align' => 'right'];

        //$lines[0][] = ['text' => __('Tax'), 'feed' => 495, 'align' => 'right'];

        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    protected function insertOrder(&$page, $obj, $putOrderId = true) {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->drawRectangle(25, $top, 570, $top - 75);

        if(file_exists($this->helper->getLogoPath())){
            $logo = \Zend_Pdf_Image::imageWithPath($this->helper->getLogoPath());
            $height = ($top - 70) - ($top - 5);
            $width = $height / ($logo->getPixelHeight()/$logo->getPixelWidth());
            $page->drawImage($logo, 560 + $width, $top -  70, 560, $top - 5);
        }
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);


        /*$page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);*/

        if ($putOrderId) {
            $page->drawText(__('Order # ') . $order->getRealOrderId(), 35, $top -= 30, 'UTF-8');
            $top +=15;
        }
        
        $group = $this->group->getById($order->getCustomerGroupId());
        $top -=30;
        $page->drawText(
            __('Order Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top,
            'UTF-8'
        );

        $top-=15;

        $page->drawText('Customer Group: '.$group->getCode(), 35, $top, 'UTF-8');

        $top -= 25;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));
        $billingAddress[] = 'E: '.$order->getCustomerEmail();
        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($this->addressRenderer->format($order->getShippingAddress(), 'pdf'));
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;
        
        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 206, $this->y - 25);
            $page->drawRectangle(206, $this->y, 387, $this->y - 25);
            $page->drawRectangle(387, $this->y, 570, $this->y - 25);
            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method'), 216, $this->y, 'UTF-8');
            $page->drawText(__('Order Comment'), 397, $this->y, 'UTF-8');
            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }
        if($order->getPoNumber() && $order->getPoNumber() != ''){
            $page->drawText('PO Number: '.$order->getPoNumber(), $paymentLeft, $yPayments, 'UTF-8');
            $yPayments -= 15;
        }
        foreach ($payment as $value) {
            if (trim($value) != '') {
                if($order->getPayment()->getCcLast4()){
                    $value.=' ending '.$order->getPayment()->getCcLast4();
                }
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 216, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . __(
                    'Total Shipping Charges'
                ) . " " . $order->formatPriceTxt(
                    $order->getShippingAmount()
                ) . ")";
            $totalShippingWeightText = '('.__('Total Shipping Weight: ').' '.($order->getWeight() + $order->getAdditionalWeight()).' lbs)';
            $this->_setFontBold($page, 10);
            $page->drawText($totalShippingWeightText, 216, $yShipments - $topMargin - $topMargin + 3, 'UTF-8'); 
            $this->_setFontRegular($page, 10);   
            $page->drawText($totalShippingChargesText, 216, $yShipments - $topMargin, 'UTF-8');
            if($order->getShippingMethod() == 'storepickup_storepickup'){
                $page->drawText('Pickup Date: '.\Carbon\Carbon::parse($order->getPickupDate())->format('m/d/Y'), 216, $yShipments + 5 /*+ ($topMargin * 3)*/, 'UTF-8');
                $page->drawText('Pickup Time: '.$order->getPickupTime(), 216, $yShipments - 5/* + ($topMargin * 4)*/, 'UTF-8');
            }
            $yShipments -= $topMargin + 10;
            if($order->getDeliveryDate()){
                $page->drawText('Requested Delivery Date: '.\Carbon\Carbon::parse($order->getDeliveryDate())->format('m/d/Y'), 216, $yShipments + $topMargin + $topMargin - 5, 'UTF-8');
                //$yShipments -= $topMargin + 10;
            }
            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments) - 12;

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right
            $estimate = null;
            $page->drawText('Estimated Shipping Time: '.$order->getTimeInTransit().' days', 216, $currentY + 7, 'UTF-8');
            /*$pieces = explode('_', $order->getShippingMethod());
            if(isset($pieces[0]) && $pieces[0] == 'ups'){
                $code = $pieces[1];
                $data = json_decode($order->getUpsData());
                if($data && is_array($data)){
                    foreach($data as $service){
                        if($service->Service->Code == $code){
                            $page->drawText('Estimated Shipping Time: '.((!is_object($service->GuaranteedDaysToDelivery) && (int)$service->GuaranteedDaysToDelivery > 0)?(int)$service->GuaranteedDaysToDelivery:4).' days', 216, $currentY + 7, 'UTF-8');
                        }
                        
                    }
                }
            }*/
            $this->y = $currentY;
            $this->y -= 15;
            foreach($order->getStatusHistoryCollection() as $comment){
                if(strpos($comment->getComment(), 'Captured') === false && strpos($comment->getComment(), 'Authorized') === false){
                    $comment = $comment->getComment();
                    $pieces = str_split($comment, 40);
                    $length = 40;
                    $depthIn = 0;
                    $y = $addressesEndY - 40;
                    $count = 0;
                    while($depthIn != strlen($comment) && $count < 5){
                        $piece = substr($comment, $depthIn, $length+1);
                        if(strlen($piece) == 41){
                            $piece = substr($piece, 0, strrpos($piece, ' '));
                        }
                        $depthIn+=strlen($piece);
                        $page->drawText(trim($piece), 397, $y, 'UTF-8');
                        $y-=10;
                        $count++;
                    } 
                }
            }
            $order->setInvoicePrinted(1);
            $order->save();
        }
    }

    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $unit = 0;
        $items = $order->getItems();
        foreach($items as $i){
            $product = $this->product->load($i->getProductId());
            $unit += ((floatval($i->getQtyWeight()) > 0)?$i->getQtyWeight():($i->getQtyOrdered() * $product->getWeight()));
        }

        $lines = [];
        $lines[0][] = ['text' => $unit.' lbs', 'feed' => 495, 'align' => 'right','font' => 'bold'];
        $lineBlock = ['lines' => $lines, 'height' => 5];

        $page = $this->drawLineBlocks($page, [$lineBlock], ['table_header' => false]);
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }

        $this->y -= 30;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }

}