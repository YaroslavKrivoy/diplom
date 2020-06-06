<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 29.10.19
 * Time: 12:08
 */

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Pdflabels;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use KozakGroup\RewriteOrderEditor\Model\Order\Shipment;
use KozakGroup\RewriteOrderEditor\Model\Order\Invoice;
use KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Items\Bulk;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;


class Many extends \Infomodus\Upslabel\Controller\Adminhtml\Pdflabels\Many
{

    const SORT_ORDER_DESC = 'DESC';

    public $_storeManager;

    protected $orderRepository;

    protected $shipment;

    protected $invoice;

    protected $bulk;

    protected $dateTime;

    protected $_redirect;


    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Infomodus\Upslabel\Model\ResourceModel\Items\Collection $labelCollection,
        Filter $filter,
        \Infomodus\Upslabel\Model\ResourceModel\Items\CollectionFactory $labelCollectionFactory,
        OrderFactory $collectionFactory, ShipmentFactory $shipmentCollectionFactory,
        CreditmemoFactory $creditmemoCollectionFactory,
        \Infomodus\Upslabel\Helper\Config $conf,
        \Infomodus\Upslabel\Helper\Pdf $pdf,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Shipment $shipment,
        Invoice $invoice,
        Bulk $bulk,
        DateTime $dateTime,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    )
    {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $labelCollection, $filter, $labelCollectionFactory, $collectionFactory, $shipmentCollectionFactory, $creditmemoCollectionFactory, $conf, $pdf, $fileFactory);
        $this->orderRepository = $orderRepository;
        $this->_storeManager = $storeManager;
        $this->shipment = $shipment;
        $this->invoice = $invoice;
        $this->bulk = $bulk;
        $this->dateTime = $dateTime;
    }

    public function massAction(AbstractCollection $collection)
    {
        $ids = ($collection->getAllIds()) ? $collection->getAllIds() : array($this->getRequest()->getParam('order_id'));
        foreach ($ids as $key=>$id){
            $order = $this->orderRepository->get($id);
            if($order->getTracksCollection()->getItems()){
                unset($ids[$key]);
            }
        }
        if (count($ids) > 0) {
            $paramName = $this->getRequest()->getParam('namespace', null);
            $this->bulk->massAction($collection);
            if ($paramName == 'sales_order_grid' || ($paramName == '')) {
                foreach ($ids as $id) {
                    $order = $this->orderRepository->get($id);
                    $this->invoice->createInvoice($order);
                    $this->shipment->createShipment($order);
                }
            }

            $labels = $this->labelCollection
                ->addFieldToFilter('lstatus', 0);
            if ($this->conf->getStoreConfig('upslabel/printing/bulk_printing_all') == 1) {
                $labels->addFieldToFilter('rva_printed', 0);
            }

            if ($paramName === null) {
                $paramName = $this->getRequest()->getParam('massaction_prepare_key', null);
            }

            switch ($paramName) {
                case 'sales_order_grid':
                    $labels->addFieldToFilter('order_id', ['in' => $ids]);
                    $errorLink = 'sales/order';
                    break;
                case 'sales_order_shipment_grid':
                    $labels->addFieldToFilter('shipment_id', ['in' => $ids])
                        ->addFieldToFilter('type', [['like' => 'shipment'], ['like' => 'invert']]);
                    $errorLink = 'sales/shipment';
                    break;
                case 'sales_order_creditmemo_grid':
                    $labels->addFieldToFilter('shipment_id', ['in' => $ids])->addFieldToFilter('type', 'refund');
                    $errorLink = 'sales/creditmemo';
                    break;
                default:
                    $labels->addFieldToFilter('order_id', ['in' => $ids]);
                    $labels->setOrder('upslabel_id', self::SORT_ORDER_DESC);
                    $labels->setPageSize(1);
                    $errorLink = 'infomodus_upslabel/items';
                    break;
            }

            $labels->load();
            if (count($labels) > 0) {
                foreach ($labels as $label){
                    $orderLabel = $this->orderRepository->get($label->getOrderId());
                    $orderLabel->addStatusHistoryComment($this->addCommentPrintLabel($label),$orderLabel->getStatus());
                    $orderLabel->save();
                }
                $data = null;
                if (count($labels) == 1 && $paramName != 'sales_order_grid') {
                    $data = $this->pdf->createPDF(strval(key($labels->getItems())));
                } else if(count($labels) >= 1){
                    $data = $this->pdf->createManyPDF($labels);
                }
                if ($data !== false && count($data->pages) > 0) {
                    $fileName = sprintf('ups_shipping_labels%s.pdf', $this->dateTime->date('Y-m-d_H-i-s'));
                    return $this->fileFactory->create(
                        $fileName,
                        $data->render(),
                        \Magento\Framework\App\Filesystem\DirectoryList::UPLOAD,
                        'application/pdf'
                    );
                } else {
                    $this->messageManager->addWarningMessage(__('For the selected items are not created PDF labels.'));
                    return $this->resultRedirectFactory->create()->setPath($errorLink . '/');
                }
            } else {
                $this->messageManager->addErrorMessage(__('For the selected items are not created labels.'));
                return $this->resultRedirectFactory->create()->setPath($errorLink . '/');
            }
        }
        else{
            return $this->resultRedirectFactory->create()->setPath('sales/order/');
        }
    }

    protected function addCommentPrintLabel($label){
        $printLabel = '<b>Print label:</b><br>';
        $printLabel .= '-Print tracking number: ' . $label->getTrackingnumber() . '<br>';
        return $printLabel;
    }

}