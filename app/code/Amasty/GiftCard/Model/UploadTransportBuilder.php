<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class UploadTransportBuilder extends TransportBuilder
{
    /**
     * @var MailMessageFactory
     */
    private $ammessageFactory;

    public function __construct(
        FactoryInterface $templateFactory,
        \Magento\Framework\Mail\MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\MessageInterfaceFactory $messageFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        /** @var \Amasty\GiftCard\Model\MailMessage message */
        $this->message = $message;
        $this->ammessageFactory = $messageFactory;
        if (version_compare($productMetaData->getVersion(), '2.3', '>')) {
            $this->message = $objectManager->create('Amasty\GiftCard\Model\MailMessage');
            $this->ammessageFactory = $objectManager->create('Amasty\GiftCard\Model\MailMessageFactory');
        }
    }

    /**
     * @param $pdf
     *
     * @return $this
     */
    public function addAttachment($pdf)
    {
        $this->message->createAttachment(
            $pdf,
            'application/pdf',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            'Gift.pdf'
        );

        return $this;
    }

    /**
     * @param $file
     * @param $id
     *
     * @return $this
     * @throws \Zend_Mail_Exception
     */
    public function attachFile($file, $id)
    {
        if (!empty($file) && file_exists($file)) {
            $part = $this->message
                ->createAttachment(
                    file_get_contents($file),
                    'IMAGE/PNG',
                    \Zend_Mime::DISPOSITION_INLINE,
                    \Zend_Mime::ENCODING_BASE64,
                    __('GiftCard.png')
                );
            $part->id = $id;
        }

        return $this;
    }

    /**
     * Reset object state
     *
     * @return $this
     */
    protected function reset()
    {
        parent::reset();
        $this->message = $this->ammessageFactory->create();

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->reset();

        return $this;
    }
}
