<?php
/**
 * OrderComments : Index.php
 * @copyright Customer Paradigm. 2018
 * @date 2/5/18 7:57 AM
 * @version 1.0.2
 * @since 1.0.2
 */

namespace CustomerParadigm\OrderComments\Controller\Index;

use CustomerParadigm\OrderComments\Logger\Logger;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistor;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Index
 * @package CustomerParadigm\OrderComments\Controller\Index
 */
class Index extends Action
{
    const QUOTE_ID = 'quoteid';
    const COMMENT = 'comment';
    const MESSAGE = 'message';
    const PASS_MESSAGE = 'OK';
    const REQUIRED_METHOD = 'setData';

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var DataPersistor
     */
    private $dataPersister;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param DataPersistor $dataPersistor
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        DataPersistor $dataPersistor,
        Logger $logger
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->dataPersister = $dataPersistor;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|null
     */
    public function execute()
    {
        $message[self::MESSAGE] = self::PASS_MESSAGE;
        $r = null;
        try {
            $params = $this->getRequest()->getParams();
            $r = $this->jsonFactory->create();
            $this->dataPersister->set($params[self::QUOTE_ID], $params[self::COMMENT]);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $message[self::MESSAGE] = $e->getMessage();
        }finally{
            if (!empty($r)
                && method_exists($r, self::REQUIRED_METHOD)
            ) {
                $r->setData(json_encode($message));
                return $r;
            }
        }
    }
}
