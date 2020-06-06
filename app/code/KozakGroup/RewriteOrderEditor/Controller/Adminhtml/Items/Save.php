<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 06.11.19
 * Time: 17:47
 */

namespace KozakGroup\RewriteOrderEditor\Controller\Adminhtml\Items;

use KozakGroup\RewriteOrderEditor\Model\Order\Shipment;
use KozakGroup\RewriteOrderEditor\Model\Order\Invoice;
use Magento\Framework\Stdlib\DateTime\DateTime;


class Save extends \Infomodus\Upslabel\Controller\Adminhtml\Items\Save
{

    protected $shipment;

    protected $invoice;

    protected $pdf;

    protected $dateTime;

    protected $fileFactory;

    public function __construct(\Magento\Backend\App\Action\Context $context,
                                \Magento\Framework\Registry $coreRegistry,
                                \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Infomodus\Upslabel\Helper\Handy $handy,
                                \Infomodus\Upslabel\Helper\Pdf $pdf,
                                DateTime $dateTime,
                                Shipment $shipment,
                                Invoice $invoice,
                                \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ){
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $handy);
        $this->shipment = $shipment;
        $this->invoice = $invoice;
        $this->pdf = $pdf;
        $this->dateTime = $dateTime;
        $this->fileFactory = $fileFactory;
    }

    public function execute()
    {

        if ($this->getRequest()->getPostValue()) {
            $orderId = $this->getRequest()->getParam('order_id');
            $type = $this->getRequest()->getParam('type');
            $shipmentId = $this->getRequest()->getParam('shipment_id', null);
            $redirectUrl = $this->getRequest()->getParam('redirect_path', null);
            $params = $this->getRequest()->getParams();
            $order = $this->_objectManager->get('Magento\Sales\Model\OrderRepository')->get($orderId);
            if ($order) {
                if (isset($params['package'])) {
                    $arrPackagesOld = $params['package'];
                    $arrPackages = [];
                    if (count($arrPackagesOld) > 0) {
                        foreach ($arrPackagesOld as $k => $v) {
                            $i = 0;
                            foreach ($v as $d => $f) {
                                $arrPackages[$i][$k] = $f;
                                $i++;
                            }
                        }
                        unset($v, $k, $i, $d, $f);
                        $params['package'] = $arrPackages;
                    }

                    $label = $this->_handy->getLabel($order, $type, $shipmentId, $params);
                    if (true === $label) {
                        $labelIds = [];
                        if (count($this->_handy->label) > 0) {
                            foreach ($this->_handy->label as $label) {
                                $labelIds[] = $label->getId();
                            }
                        }
                        if (count($this->_handy->label2) > 0) {
                            foreach ($this->_handy->label2 as $label2) {
                                $labelIds[] = $label2->getId();
                            }
                        }

                        $this->invoice->createInvoice($order);
                        $this->shipment->createShipment($order);

                        $order->addStatusHistoryComment($this->addCommentPrintLabel($label),$order->getStatus());
                        $order->save();

                        $data = $this->pdf->createPDF($labelIds[0]);


                        if ($data !== false && count($data->pages) > 0) {
                            $fileName = sprintf('ups_shipping_labels%s.pdf', $this->dateTime->date('Y-m-d_H-i-s'));
                            $this->fileFactory->create(
                                $fileName,
                                $data->render(),
                                \Magento\Framework\App\Filesystem\DirectoryList::UPLOAD,
                                'application/pdf'
                            );
                        }
//                        $this->_redirect(
//                            'infomodus_upslabel/items/show',
//                            [
//                                'label_ids' => implode(',', $labelIds),
//                                'type' => $type,
//                                'redirect_path' => $redirectUrl
//                            ]
//                        );
                        return;

                    } else {
                        $this->messageManager->addErrorMessage(__('Error 10002.'));
                        $this->_redirect('infomodus_upslabel/*');
                        return;
                    }
                } else {
                    $this->messageManager
                        ->addErrorMessage(__('There must be at least one package. Add the package please'));
                    $this->_redirect('infomodus_upslabel/*');
                    return;
                }
            } else {
                $this->messageManager
                    ->addErrorMessage(__('Order is wrong'));
                $this->_redirect('infomodus_upslabel/*');
                return;
            }
        }

        $this->_redirect('infomodus_upslabel/*/');
    }

    protected function addCommentPrintLabel($label){
        $labelComment = '<b>Print label:</b><br>';
        $labelComment .= '-Print tracking number: ' . $label->getShipmentidentificationnumber() . '<br>';
        $labelComment .= '<b>Created label:</b><br>';
        $labelComment .= '-Created tracking number: '. $label->getShipmentidentificationnumber() . '<br>';
        return $labelComment;
    }

}