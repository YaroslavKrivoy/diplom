<?php
/**
 * OrderComments : AllPaymentInformationManagementPlugin.phpopyright Customer Paradigm. 2018
 * @date 2/5/18 7:57 AM
 * @version 1.0.2
 * @since 1.0.2
 */

namespace CustomerParadigm\OrderComments\Model\Checkout;

use CustomerParadigm\OrderComments\Logger\Logger;
use CustomerParadigm\OrderComments\Model\Checkout;
use Magento\Quote\Model\QuoteManagement\Interceptor;
use Magento\Framework\App\Request\DataPersistor;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Sales\Model\OrderFactory;

/**
 * Class AllPaymentInformationManagementPlugin
 * @package CustomerParadigm\OrderComments\Model\Checkout
 */
class AllPaymentInformationManagementPlugin extends Checkout
{

    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     */
    private $historyFactory;
    /**
     * @var \Magento\Sales\Model\OrderFactory $orderFactory
     */
    private $orderFactory;
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;
    /**
     * @var \Magento\Framework\App\Request\DataPersistor
     */
    private $dataPersistor;
    /**
     * @var \CustomerParadigm\OrderComments\Logger\Logger;
     */
    private $logger;

    /**
     * Checkout constructor.
     * @param HistoryFactory $historyFactory
     * @param OrderFactory $orderFactory
     * @param QuoteRepository $quoteRepository
     * @param DataPersistor $dataPersistor
     * @param Logger $logger
     */
    public function __construct(
        HistoryFactory $historyFactory,
        OrderFactory $orderFactory,
        QuoteRepository $quoteRepository,
        DataPersistor $dataPersistor,
        Logger $logger
    ) {
        $this->historyFactory = $historyFactory;
        $this->orderFactory = $orderFactory;
        $this->quoteRepository = $quoteRepository;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
    }

    /**
     * @param Interceptor $interceptor
     * @param integer $orderId
     * @return integer
     */
    public function afterPlaceOrder(
        Interceptor $interceptor,
        $orderId
    ) {
        try {
            /**
             * @var \Magento\Sales\Model\OrderFactory $order
             */
            $order = $this->orderFactory->create()->load($orderId);
            /**
             * @var string
             */
            $comment = '';
            /**
             * @var string $status
             */
            $status = '';
            /**
             * @var \Magento\Sales\Model\Order\Status\HistoryFactory
             */
            $history = null;

            /*
             * If the data 'persistor' does not contain a comment value, we'll see if the request body has one.s
             */
            if (empty($this->dataPersistor->get($order->getQuoteId()))) {
                // Check to see if the input body has anything.
                $request_body = file_get_contents('php://input');
                $data = json_decode($request_body, true);
                $this->dataPersistor->set($order->getQuoteId(), $data['comments']);

                if (empty($data['comments'])) {
                    throw new \UnexpectedValueException("It is the end of the world! No comment was given.");
                }
            }

            $comment = parent::COMMENT_PREFIX . $this->dataPersistor->get($order->getQuoteId());

            // make sure $order exists
            if ($order->getData(parent::ENTITY_ID)) {
                $status = $order->getData(parent::STATUS);
                $history = $this->historyFactory->create();
                $history->setData(parent::COMMENT, $comment);
                $history->setData(parent::PARENT_ID, $orderId);
                $history->setData(parent::VISIBLE_ON_FRONT, 1);
                $history->setData(parent::CUSTOMER_NOTIFIED, 0);
                $history->setData(parent::ENTITY_NAME, parent::ORDER);
                $history->setData(parent::STATUS, $status);
                $history->save();
            }
        } catch (\UnexpectedValueException $e) {
            $this->logger->info($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        } finally {
            return $orderId;
        }
    }
}
