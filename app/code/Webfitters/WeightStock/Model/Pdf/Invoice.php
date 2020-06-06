<?php
namespace Webfitters\WeightStock\Model\Pdf;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice {

    protected $product;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->product = $product;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $string,
            $resource,
            $resourceCollection,
            $data
        );
    }

    public function draw() {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];
        $lines[0] = [['text' => $this->string->split($item->getName(), 35, true, true), 'feed' => 35]];
        $lines[0][] = [
            'text' => $this->string->split($this->getSku($item), 17),
            'feed' => 290,
            'align' => 'right',
        ];
        $product = $this->product->load($item->getProductId());
        $weight = (floatval($item->getQtyWeight()) > 0)?$item->getQtyWeight().' lbs':'--';
        $weightlb = (floatval($item->getQtyWeight()) > 0)?'$'.number_format($item->getRowTotal() / $item->getQtyWeight() / $item->getQty(), 2):'--';
        $unitweight = 0;
        if(floatval($item->getQtyWeight()) > 0){
            $unitweight = $item->getQtyWeight() * $item->getCaseQty();
        } else {
            $unitweight = $item->getQty() * $product->getWeight();
        }
        $lines[0][] = ['text' => ($item->getCaseQty() > 0)?$item->getCaseQty():$item->getQty() * 1, 'feed' => 445, 'align' => 'right'];
        $lines[0][] = ['text' => $weight, 'feed' => 395, 'align' => 'right'];
        $lines[0][] = ['text' => $weightlb, 'feed' => 485, 'align' => 'right'];
        $lines[0][] = ['text' => $unitweight, 'feed' => 515, 'align' => 'right'];
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 355;
        $feedSubtotal = 565;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
                $i++;
            }
            // draw Price
            $lines[$i][] = [
                'text' => $priceData['price'],
                'feed' => $feedPrice,
                'font' => 'bold',
                'align' => 'right',
            ];
            // draw Subtotal
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => $feedSubtotal,
                'font' => 'bold',
                'align' => 'right',
            ];
            $i++;
        }

        // draw Tax
        /*$lines[0][] = [
            'text' => $order->formatPriceTxt($item->getTaxAmount()),
            'feed' => 495,
            'font' => 'bold',
            'align' => 'right',
        ];*/

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
                ];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 40];
                    }
                }
            }
        }
        $lineBlock = ['lines' => $lines, 'height' => 15];
        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->drawLine(25, $pdf->y + 11, 570, $pdf->y + 11);
        $this->setPage($page);
    }

}