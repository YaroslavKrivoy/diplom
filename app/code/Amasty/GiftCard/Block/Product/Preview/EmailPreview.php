<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Block\Product\Preview;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Amasty\GiftCard\Model\Repository\ImageRepository;
use Amasty\GiftCard\Api\Data\AmGiftCardOptionsInterface;
use Amasty\GiftCard\Model\ImageProcessor;
use Amasty\GiftCard\Api\Data\ImageInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\App\Area;
use Amasty\GiftCard\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Mail\TemplateInterface;

/**
 * EmailPreview block
 */
class EmailPreview extends Template
{
    const GIFT_CARD_BASE_TEMPLATE = 'amgiftcard_email_email_template';

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var ImageInterface
     */
    private $imageModel;

    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        Context $context,
        ImageRepository $imageRepository,
        ImageProcessor $imageProcessor,
        ImageInterface $imageModel,
        FactoryInterface $templateFactory,
        Config $config,
        ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->imageRepository = $imageRepository;
        $this->imageProcessor = $imageProcessor;
        $this->imageModel = $imageModel;
        $this->templateFactory = $templateFactory;
        $this->config = $config;
        $this->storeManager = $context->getStoreManager();
        $this->messageManager = $messageManager;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getEmailContent()
    {
        $variables = $this->getEmailVariables();

        try {
            $templateId = $this->config->getGiftCardEmailTemplate();
            $template = $this->templateFactory->get($templateId);
            $template = $this->applyVarsAndOptions($template, $variables);

            $result = $template->processTemplate();
        } catch (\Exception $e) {
            $template = $this->templateFactory->get(self::GIFT_CARD_BASE_TEMPLATE);
            $template = $this->applyVarsAndOptions($template, $variables);

            $result = $template->processTemplate();
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getEmailVariables()
    {
        $isExistFile = count($this->getRequest()->getFiles());
        $requestParams = $this->getRequest()->getParams();

        $variables['currency_code'] = __('$');
        $variables['initial_value'] = __('XXX');
        $variables['gift_code'] = __('GIFTCARDCODE');
        $variables['recipient_name'] = __('Gift Card Recipient Name');
        $variables['sender_name'] = __('Your Name');
        $variables['sender_email'] = __('Your Email');
        $variables['sender_message'] = __('Additional message for Gift Card');

        if (!empty($requestParams[AmGiftCardOptionsInterface::RECIPIENT_NAME])) {
            $variables['recipient_name'] = $requestParams[AmGiftCardOptionsInterface::RECIPIENT_NAME];
        }
        if (!empty($requestParams[AmGiftCardOptionsInterface::SENDER_NAME])) {
            $variables['sender_name'] = $requestParams[AmGiftCardOptionsInterface::SENDER_NAME];
        }
        if (!empty($requestParams[AmGiftCardOptionsInterface::SENDER_EMAIL])) {
            $variables['sender_email'] = $requestParams[AmGiftCardOptionsInterface::SENDER_EMAIL];
        }
        if (!empty($requestParams[AmGiftCardOptionsInterface::MESSAGE])) {
            $variables['sender_message'] = $requestParams[AmGiftCardOptionsInterface::MESSAGE];
        }

        if ($requestParams[AmGiftCardOptionsInterface::CARD_AMOUNT] == 'custom') {
            if ($requestParams[AmGiftCardOptionsInterface::CARD_VALUE]) {
                $variables['initial_value'] = $requestParams[AmGiftCardOptionsInterface::CARD_VALUE];
            }
        } elseif ($requestParams[AmGiftCardOptionsInterface::CARD_AMOUNT]) {
            $variables['initial_value'] = $requestParams[AmGiftCardOptionsInterface::CARD_AMOUNT];
        }

        if ($isExistFile && !$requestParams[AmGiftCardOptionsInterface::IMAGE]) {
            $file = $this->getRequest()->getFiles('amgiftcard-userimage-input');
            $image = $this->imageProcessor->processImageSaving($file);
            $totalImage = $this->imageModel->getImagePathByName($image);
        } else {
            $image = $this->imageRepository->getById($requestParams[AmGiftCardOptionsInterface::IMAGE]);
            $totalImage = $image->getImagePath();
        }
        $variables['image_base64'] = $totalImage;

        return $variables;
    }

    /**
     * @param TemplateInterface $template
     * @param array $variables
     *
     * @return mixed
     */
    private function applyVarsAndOptions($template, $variables)
    {
        /** TemplateInterface $template*/
        return $template->setVars($variables)
            ->setOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId()
                ]
            );
    }
}
