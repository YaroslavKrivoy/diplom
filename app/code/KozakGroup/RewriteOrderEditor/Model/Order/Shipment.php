<?php
/**
 * Created by PhpStorm.
 * User: admin-i3-5
 * Date: 20.09.19
 * Time: 16:06
 */

namespace KozakGroup\RewriteOrderEditor\Model\Order;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order\Pdf\Shipment as ShipmentPdf;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\RequestInterface;


class Shipment
{

    protected $orderRepository;

    protected $shipmentFactory;

    protected $shipmentRepository;

    protected $shipmentNotifier;

    protected $pdfShipment;

    protected $fileFactory;

    protected $dateTime;

    protected $_objectManager;

    protected $request;

    protected $_filesystem;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        ShipmentPdf $pdfShipment,
        FileFactory $fileFactory,
        DateTime $dateTime,
        ObjectManagerInterface $_objectManager,
        RequestInterface $request,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->orderRepository = $orderRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->pdfShipment = $pdfShipment;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->_objectManager = $_objectManager;
        $this->request = $request;
        $this->_filesystem = $filesystem;
    }

    public function createShipment($order)
    {
        $convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
        $shipment = $convertOrder->toShipment($order);
        $track = null;

        $data = ['carrier_code' => $this->request->getParam('carrier_code'),
            'title' => $this->request->getParam('title'),
            'number' => $this->request->getParam('number')];

        if (!empty(array_filter($data)) && !empty($data['number'])) {
            $track = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory')->create()->addData($data);
        }

        if ($order->canShip()) {

            foreach ($order->getAllItems() AS $orderItem) {

                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }

                $qtyShipped = $orderItem->getQtyToShip();

                $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                $shipment->addItem($shipmentItem);
            }

            $shipment->register();

            $shipment->getOrder()->setIsInProcess(true);

            try {
                if ($track) {
                    $shipment->addTrack($track)->save();
                }
                $shipment->save();
                $shipment->getOrder()->save();

                return $this->sendAndPrintShipment($shipment);

            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        } else {
            $shipmentCollection = $shipment->getCollection()->addAttributeToFilter('order_id', $order->getId());
            foreach ($shipmentCollection as $item) {
                $shipmentData = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($item->getIncrementId());
                if (!$shipmentData->getTracks()) {
                    if ($track) {
                        $shipmentData->addTrack($track)->save();
                    }
                    return $this->sendAndPrintShipment($shipmentData);
                } else {
                    return $this->sendAndPrintShipment($shipmentData);
                }
            }
        }
    }


    public function sendAndPrintShipment($shipment)
    {
        $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
            ->notify($shipment);
        $fileName = sprintf('shipment%s.pdf', $this->dateTime->date('Y-m-d_H-i-s'));
        $content = $this->pdfShipment->getPdf(array($shipment))->render();
        $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::UPLOAD);
        $dir->writeFile($fileName, $content);
        return $fileName;
    }
}