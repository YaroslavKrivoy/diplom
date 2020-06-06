<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OrdersBase\Observer;

use Magento\Framework\App\Area;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\OrdersBase\Model\DeviceData;

class OrderPlaced implements ObserverInterface
{
    /**
     * @var \DeviceDetector\DeviceDetectorFactory
     */
    protected $deviceDetectorFactory;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface
     */
    protected $deviceDataRepository;

    /**
     * @var \MageWorx\OrdersBase\Helper\Data
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * OrderPlaced constructor.
     * @param \DeviceDetector\DeviceDetectorFactory $deviceDetectorFactory
     * @param \Magento\Framework\HTTP\Header $httpHeader
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \DeviceDetector\DeviceDetectorFactory $deviceDetectorFactory,
        \Magento\Framework\HTTP\Header $httpHeader,
        \Magento\Framework\App\State $state,
        \MageWorx\OrdersBase\Api\DeviceDataRepositoryInterface $deviceDataRepository,
        \MageWorx\OrdersBase\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->deviceDetectorFactory = $deviceDetectorFactory;
        $this->httpHeader = $httpHeader;
        $this->state = $state;
        $this->deviceDataRepository = $deviceDataRepository;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();

        try {
            $deviceDataEntity = $this->deviceDataRepository->getByOrderId($orderId);
        } catch (NoSuchEntityException $exception) {
            $deviceDataEntity = $this->deviceDataRepository->getEmptyEntity();
        }

        // Do not save many times in case when event was trigger twice
        if ($deviceDataEntity->getId()) {
            return;
        }

        try {
            $userAgent = $this->httpHeader->getHttpUserAgent();
            if (!is_string($userAgent)) {
                if (is_array($userAgent)) {
                    $userAgent = implode(' ', $userAgent);
                } elseif (is_object($userAgent)) {
                    /** @var object $userAgent */
                    $userAgent = $userAgent->__toString();
                } else {
                    throw new LocalizedException(__('Unable to detect user agent.'));
                }
            }
            /** @var \DeviceDetector\DeviceDetector $deviceDetector */
            $deviceDetector = $this->deviceDetectorFactory->create(['userAgent' => $userAgent]);
            $deviceDetector->discardBotInformation();
            $deviceDetector->parse();

            if (!$deviceDetector->isBot()) {
                $deviceCode = $deviceDetector->getDevice();
                $areaCode = $this->getAreaCode();

                $deviceDataEntity->setDeviceCode($deviceCode)
                    ->setAreaCode($areaCode)
                    ->setOrderId($orderId);

                $this->deviceDataRepository->save($deviceDataEntity);
            }
        } catch (\Exception $e) {
            // Do not break the checkout porcess in case any error fired
            $this->logger->warning($e->getMessage());
        }

        return;
    }

    /**
     * Get current area code
     *
     * @return int
     */
    private function getAreaCode()
    {
        $referer = $this->httpHeader->getHttpReferer(true);
        $urlPath = parse_url($referer, PHP_URL_PATH);
        $checkoutBasePath = $this->helper->getCheckoutUrl();
        $searchablePath = '/' . $checkoutBasePath . '/';

        if ($this->state->getAreaCode() == Area::AREA_FRONTEND) {
            $areaCode = DeviceData::AREA_FRONT;
        } elseif ($this->state->getAreaCode() == Area::AREA_ADMINHTML) {
            $areaCode = DeviceData::AREA_ADMIN;
        } elseif ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST) {
            if (preg_match($searchablePath, $urlPath)) {
                $areaCode = DeviceData::AREA_FRONT;
            } else {
                $areaCode = DeviceData::AREA_REST;
            }
        } elseif ($this->state->getAreaCode() == Area::AREA_WEBAPI_SOAP) {
            $areaCode = DeviceData::AREA_SOAP;
        } else {
            $areaCode = DeviceData::AREA_UNKNOWN;
        }

        return $areaCode;
    }
}
