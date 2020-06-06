<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\AccountInterface;
use Amasty\GiftCard\Model\Repository\ImageRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Account extends AbstractModel implements AccountInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_USED = 3;

    const FONT_FILE_ARIAL = 'amasty_giftcard/arial_bold.ttf';

    protected $imagePath = 'amasty_giftcard/generated_images_cache';

    /**
     * @var Code
     */
    protected $codeModel;

    /**
     * @var ResourceModel\Code
     */
    protected $codeResourceModel;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $orderModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var Image
     */
    protected $imageModel;

    /**
     * @var UploadTransportBuilder
     */
    protected $uploadTransportBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResourceModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Pdf
     */
    private $pdf;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\GiftCard\Model\ResourceModel\Account $resource,
        \Amasty\GiftCard\Model\ResourceModel\Account\Collection $resourceCollection,
        \Amasty\GiftCard\Model\Code $codeModel,
        \Amasty\GiftCard\Model\ResourceModel\Code $codeResourceModel,
        \Magento\Sales\Model\Order $orderModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Amasty\GiftCard\Model\Image $imageModel,
        \Amasty\GiftCard\Model\UploadTransportBuilder $uploadTransportBuilder,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Amasty\GiftCard\Model\Config $config,
        \Amasty\GiftCard\Model\Pdf $pdf,
        ImageRepository $imageRepository,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->codeModel = $codeModel;
        $this->codeResourceModel = $codeResourceModel;
        $this->orderModel = $orderModel;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
        $this->imageModel = $imageModel;
        $this->uploadTransportBuilder = $uploadTransportBuilder;
        $this->productResourceModel = $productResourceModel;
        $this->date = $date;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->priceCurrency = $priceCurrency;
        $this->dataHelper = $dataHelper;
        $this->file = $file;
        $this->config = $config;
        $this->pdf = $pdf;
        $this->imageRepository = $imageRepository;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\GiftCard\Model\ResourceModel\Account');
        $this->setIdFieldName('account_id');
    }

    public function getListStatuses()
    {
        return [
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_EXPIRED => __('Expired'),
            self::STATUS_USED => __('Used'),
        ];
    }

    public function generateCode($codeSetId = null)
    {
        if ($codeSetId === null) {
            $codeSetId = $this->getCodeSetId();
        }
        $code = $this->codeModel->loadFreeCode($codeSetId);
        if (!$code->getId() || $code->getUsed()) {
            throw new LocalizedException(__('Not enough free codes in the pool.'));
        }

        $this->setCodeId($code->getId());

        $code->setUsed(1);
        if ($code->getStoredData()) {
            $code->storedData = [];
        }
        $this->codeResourceModel->save($code);

        return $code;
    }

    public function getCode()
    {
        if (!$code = $this->getData('code')) {
            $code = $this->getCodeModel()->getCode();
            $this->setData('code', $code);
        }

        return $code;
    }

    public function getCodeModel()
    {
        if (!$codeModel = $this->getData('codeModel')) {
            $this->codeResourceModel->load($this->codeModel, $this->getCodeId());
            $codeModel = $this->codeModel;
            $this->setData('codeModel', $codeModel);
        }

        return $codeModel;
    }

    public function getOrder()
    {
        if (!$order = $this->getData('order')) {
            $this->orderModel->getResource()->load($this->orderModel, $this->getOrderId());
            $order = $this->orderModel;
            $this->setData('order', $order);
        }

        return $order;
    }

    /**
     * @param null $data
     *
     * @return bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Zend_Mail_Exception
     */
    public function sendDataToMail($data = null)
    {
        if (!$this->getData('recipient_email') || $this->getIsSent()) {
            return false;
        }
        $baseToOrderRate = $data && !is_array($data) ? $data->getOrder()->getBaseToOrderRate() : 1;

        $storeId = $this->getOrder()->getStoreId();
        if ($this->getData('store_id')) {
            $storeId = $this->getData('store_id');
        }

        $storeId = $this->storeManager->getStore($storeId)->getStoreId();

        $template = $this->scopeConfig->getValue(
            'amgiftcard/email/email_template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $imageGiftCard = null;
        $id = null;
        if ($this->isImage()) {
            $mediaUrl = $this->storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $imageGiftCard = $mediaUrl . $this->imagePath . DIRECTORY_SEPARATOR . $this->getImagePath();
        }

        $templateParams = [
            'recipient_name' => $this->getData('recipient_name'),
            'sender_name' => $this->getData('sender_name'),
            'sender_email' => $this->getData('sender_email'),
            'initial_value' => $this->getData('initial_value') * $baseToOrderRate,
            'currency_code' => $this->getOrder()->getOrderCurrencyCode(),
            'sender_message' => $this->getData('sender_message'),
            'gift_code' => $this->getCode(),
            'image_base64' => $imageGiftCard,
            'expired_date' => $this->date->date('Y-m-d', $this->getData('expired_date')),
        ];

        $from = $this->scopeConfig->getValue(
            'amgiftcard/email/email_identity',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($emailCC = trim($this->scopeConfig->getValue(
            'amgiftcard/email/email_recepient_cc',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ))) {
            $emailCC = explode(",", $emailCC);
            array_walk($emailCC, 'trim');
        }

        $transportBuilder = clone $this->uploadTransportBuilder;
        $fileImage = $this->getImageWithCodePath();
        $transportBuilder->clear()
            ->setTemplateIdentifier($template)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($from)
            ->addTo($this->getData('recipient_email'), $this->getData('recipient_name'))
            ->attachFile($fileImage, $id);
        if ($this->config->getScopeValue('email/attach_gift_card')) {
            $pdf = $this->pdf->create($fileImage);
            $transportBuilder->addAttachment($pdf);
        }
        if ($emailCC) {
            $transportBuilder->addCc($emailCC);
        }
        $transport = $transportBuilder->getTransport();

        $transport->sendMessage();

        if ($this->scopeConfig->getValue(
            'amgiftcard/email/send_confirmation_to_sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
            && $this->getData('sender_email')
        ) {

            $templateParams = [
                'recipient_name' => $this->getData('recipient_name'),
                'sender_name' => $this->getData('sender_name'),
                'initial_value' => $this->getData('initial_value') * $baseToOrderRate,
                'currency_code' => $this->getOrder()->getOrderCurrencyCode(),
                'sender_message' => $this->getData('sender_message'),
                'expired_date' => $this->date->date('Y-m-d', $this->getData('expired_date'))
            ];

            $transportBuilder = clone $this->uploadTransportBuilder;
            $transport = $transportBuilder->clear()
                ->setTemplateIdentifier($this->scopeConfig->getValue(
                    'amgiftcard/email/email_template_confirmation_to_sender',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ))
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars($templateParams)
                ->setFrom($from)
                ->addTo($this->getData('sender_email'), $this->getData('sender_name'))
                ->getTransport();

            $transport->sendMessage();
        }

        $this->setIsSent(1);
        $this->getResource()->save($this);
    }

    public function sendExpiryNotification()
    {
        if (!$this->getData('recipient_email')) {
            return false;
        }

        $storeId = 0;

        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId() ?: 0;
        }

        $from = $this->scopeConfig->getValue(
            'amgiftcard/email/email_identity',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $template = $this->scopeConfig->getValue(
            'amgiftcard/email/email_template_notify',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $templateParams = [
            'recipient_name' => $this->getData('recipient_name'),
            'gift_code' => $this->getCode(),
            'expired_date' => $this->date->date('Y-m-d', $this->getData('expired_date'))
        ];

        $transport = clone $this->uploadTransportBuilder;
        $transport = $transport->clear()->setTemplateIdentifier($template)
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($from)
            ->addTo($this->getData('recipient_email'), $this->getData('recipient_name'))
            ->getTransport();

        $transport->sendMessage();
    }

    public function getImageWithCodePath()
    {
        if (!$this->isImage()) {
            return '';
        }

        return $this->getImageDirPath() . $this->getImagePath();
    }

    public function isImage()
    {
        return (bool)$this->getImageId();
    }

    public function getImageDirPath()
    {
        $mediaDir = $this->directoryList->getPath('media');
        $DS = DIRECTORY_SEPARATOR;
        if (!file_exists($mediaDir . $DS . $this->imagePath)) {
            mkdir($mediaDir . $DS . $this->imagePath);
        }

        return $mediaDir . $DS . $this->imagePath . $DS;
    }

    public function getImage()
    {
        if (!$image = $this->getData('image')) {
            $this->imageModel->getResource()->load($this->imageModel, $this->getImageId());
            $image = $this->imageModel;
            $this->setData('image', $image);
        }

        return $image;
    }

    protected function _buildImage()
    {
        if (!$this->isImage()) {
            return null;
        }
        $image = $this->getImage();

        $imageInfo = getimagesize($image->getImagePath());
        if (!$imageInfo) {
            return null;
        }

        $imageResource = null;

        switch ($imageInfo['mime']) {
            case 'image/png':
                $imageResource = imagecreatefrompng($image->getImagePath());
                break;
            case 'image/gif':
                $imageResource = imagecreatefromgif($image->getImagePath());
                break;
            case 'image/jpeg':
            default:
                $imageResource = imagecreatefromjpeg($image->getImagePath());
                break;
        }

        $DS = DIRECTORY_SEPARATOR;

        $color = imagecolorallocate($imageResource, 0, 0, 0);        // Black
        $fontFile = $this->directoryList->getPath('media') . $DS . self::FONT_FILE_ARIAL;
        $fontSize = 15;

        @imagettftext(
            $imageResource,
            $fontSize,
            0,
            (int)$this->getImage()->getCodePosX(),
            $this->getImage()->getCodePosY() + $fontSize + 2,
            $color,
            $fontFile,
            $this->getCode()
        );

        $imagePath = uniqid() . '_' . preg_replace("/[^A-Za-z0-9_-]/", "", $this->getCode());

        switch ($imageInfo['mime']) {
            case 'image/png':
                $imagePath .= '.png';
                imagepng($imageResource, $this->getImageDirPath() . $imagePath);
                break;
            case 'image/gif':
                $imagePath .= '.gif';
                imagegif($imageResource, $this->getImageDirPath() . $imagePath);
                break;
            case 'image/jpeg':
            default:
                $imagePath .= '.jpg';
                imagejpeg($imageResource, $this->getImageDirPath() . $imagePath);
                break;
        }

        imagedestroy($imageResource);

        return $imagePath;
    }

    public function createAccount($data)
    {
        $product = $data->getOrderItem()->getProduct();
        $codeSetId = $product->getAmGiftcardCodeSet();
        if (!$codeSetId) {
            $codeSetId = $this->productResourceModel->getAttributeRawValue(
                $product->getId(),
                'am_giftcard_code_set',
                $data->getOrder()->getStoreId()
            );
        }
        try {
            $code = $this->generateCode($codeSetId);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        $productOptions = $data->getProductOptions();

        $dateDelivery = isset($productOptions['am_giftcard_date_delivery'])
            ? $productOptions['am_giftcard_date_delivery']
            : $this->date->gmtDate('Y-m-d');
        $this->setData([
            'code_id' => $code->getId(),
            'image_id' => isset($productOptions['am_giftcard_image']) ? $productOptions['am_giftcard_image'] : null,
            'buyer_id' => $this->_getCustomerId(),
            'order_id' => $data->getOrder()->getId(),
            'website_id' => $data->getWebsiteId(),
            'product_id' => $product->getId(),
            'status_id' => self::STATUS_ACTIVE,
            'initial_value' => $data->getAmount(),
            'current_value' => $data->getAmount(),
            'sender_name' => isset($productOptions['am_giftcard_sender_name'])
                ? $productOptions['am_giftcard_sender_name'] : null,
            'sender_email' => isset($productOptions['am_giftcard_sender_email'])
                ? $productOptions['am_giftcard_sender_email'] : null,
            'recipient_name' => isset($productOptions['am_giftcard_recipient_name'])
                ? $productOptions['am_giftcard_recipient_name'] : null,
            'recipient_email' => isset($productOptions['am_giftcard_recipient_email'])
                ? $productOptions['am_giftcard_recipient_email'] : null,
            'sender_message' => isset($productOptions['am_giftcard_message'])
                ? $productOptions['am_giftcard_message'] : null,
            'giftcard_type' => isset($productOptions['am_giftcard_type'])
                ? $productOptions['am_giftcard_type'] : null,
            'date_delivery' => $dateDelivery,
            'customer_created_id' => $data->getCustomerId()
        ]);

        if ($lifetime = $data->getLifetime()) {
            $expiredDate = $this->date->gmtDate('Y-m-d H:i:s', $dateDelivery . "+{$lifetime} days");
            $this->setData('expired_date', $expiredDate);
        }

        $this->getResource()->save($this);
        $code->setUsed(1)->save();

        $currentDate = $this->date->gmtDate('Y-m-d H:i:s');

        if ((strtotime($dateDelivery) <= strtotime($currentDate))
            && $this->getGiftcardType() != GiftCard::TYPE_PRINTED
        ) {
            $this->sendDataToMail($data);
        }

        return $this;
    }

    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);

        return $this;
    }

    protected function _getCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        } else {
            $customerId = null;
        }

        return $customerId;
    }

    public function isValid($website = null)
    {
        if (!$this->getId()) {
            throw new LocalizedException(__('Wrong gift card code'));

            return false;
        }

        $website = $this->storeManager->getWebsite($website)->getId();
        if ($this->getWebsiteId() != $website) {
            throw new LocalizedException(__('Wrong gift card website'));

            return false;
        }

        if ($this->getStatusId() != self::STATUS_ACTIVE) {
            if ($this->getStatusId() == self::STATUS_EXPIRED) {
                throw new LocalizedException(__('Gift card %1 is expired.', $this->getCode()));
            } elseif ($this->getStatusId() == self::STATUS_USED) {
                throw new LocalizedException(__('Gift card %1 is used.', $this->getCode()));
            } else {
                throw new LocalizedException(__('Gift card %1 is not enabled.', $this->getCode()));
            }

            return false;
        }

        if ($this->isExpired()) {
            throw new LocalizedException(__('Gift card %1 is expired.', $this->getCode()));

            return false;
        }

        if ($this->getCurrentValue() <= 0) {
            throw new LocalizedException(__('Gift card %1  balance does not have funds.', $this->getCode()));

            return false;
        }

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function canApplyCardForQuote(\Magento\Quote\Model\Quote $quote)
    {
        if ($quote->getGrandTotal() <= 0) {
            throw new LocalizedException(__('Gift card can\'t be applied. Maximum discount reached.'));
        }

        $website = $this->storeManager->getStore($quote->getStoreId())->getWebsiteId();
        $customerId = $this->_getCustomerId();
        $allowThemselves = $this->config->getAllowThemselves();
        if ($this->isValid($website)) {
            if (!$allowThemselves && $this->getCustomerCreatedId() && $this->getCustomerCreatedId() == $customerId) {
                throw new LocalizedException(__('Please be aware that it is not possible to use 
                the gift card you purchased for your own orders.'));

                return false;
            }

            return true;
        }

        return false;
    }

    public function isExpired()
    {
        if (!$this->getExpiredDate()) {
            return false;
        }
        $currentDate = $this->date->gmtDate('Y-m-d H:i:s');
        if (strtotime($this->getExpiredDate()) < strtotime($currentDate)) {
            return true;
        }

        return false;
    }

    public function isValidBool($website = null)
    {
        $isValid = true;
        try {
            $this->isValid($website);
        } catch (\Exception $e) {
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * @param int|null $statusId
     *
     * @return string
     */
    public function getStatus($statusId = null)
    {
        if ($statusId === null) {
            $statusId = $this->getStatusId();
        }
        $listStatuses = $this->getListStatuses();

        return isset($listStatuses[$statusId]) ? $listStatuses[$statusId] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountId()
    {
        return $this->_getData(AccountInterface::ACCOUNT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountId($accountId)
    {
        $this->setData(AccountInterface::ACCOUNT_ID, $accountId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeId()
    {
        return $this->_getData(AccountInterface::CODE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeId($codeId)
    {
        $this->setData(AccountInterface::CODE_ID, $codeId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageId()
    {
        return $this->_getData(AccountInterface::IMAGE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageId($imageId)
    {
        $this->setData(AccountInterface::IMAGE_ID, $imageId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->_getData(AccountInterface::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        $this->setData(AccountInterface::ORDER_ID, $orderId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->_getData(AccountInterface::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(AccountInterface::WEBSITE_ID, $websiteId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->_getData(AccountInterface::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        $this->setData(AccountInterface::PRODUCT_ID, $productId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusId()
    {
        return $this->_getData(AccountInterface::STATUS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusId($statusId)
    {
        $this->setData(AccountInterface::STATUS_ID, $statusId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInitialValue()
    {
        return $this->_getData(AccountInterface::INITIAL_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setInitialValue($initialValue)
    {
        $this->setData(AccountInterface::INITIAL_VALUE, $initialValue);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentValue()
    {
        return $this->_getData(AccountInterface::CURRENT_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentValue($currentValue)
    {
        $this->setData(AccountInterface::CURRENT_VALUE, $currentValue);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiredDate()
    {
        return $this->_getData(AccountInterface::EXPIRED_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiredDate($expiredDate)
    {
        $this->setData(AccountInterface::EXPIRED_DATE, $expiredDate);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->_getData(AccountInterface::COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->setData(AccountInterface::COMMENT, $comment);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName()
    {
        return $this->_getData(AccountInterface::SENDER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderName($senderName)
    {
        $this->setData(AccountInterface::SENDER_NAME, $senderName);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderEmail()
    {
        return $this->_getData(AccountInterface::SENDER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderEmail($senderEmail)
    {
        $this->setData(AccountInterface::SENDER_EMAIL, $senderEmail);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientName()
    {
        return $this->_getData(AccountInterface::RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName($recipientName)
    {
        $this->setData(AccountInterface::RECIPIENT_NAME, $recipientName);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail()
    {
        return $this->_getData(AccountInterface::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($recipientEmail)
    {
        $this->setData(AccountInterface::RECIPIENT_EMAIL, $recipientEmail);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderMessage()
    {
        return $this->_getData(AccountInterface::SENDER_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderMessage($senderMessage)
    {
        $this->setData(AccountInterface::SENDER_MESSAGE, $senderMessage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagePath()
    {
        if (!$this->isImage()) {
            return '';
        }
        $imagePath = $this->_getData(AccountInterface::IMAGE_PATH);
        if (!$imagePath || !$this->file->isFile($this->getImageDirPath() . $imagePath)) {
            $imagePath = $this->_buildImage();
            $this->setData('image_path', $imagePath);
            $this->getResource()->save($this);
        }

        return $imagePath;
    }

    /**
     * {@inheritdoc}
     */
    public function setImagePath($imagePath)
    {
        $this->setData(AccountInterface::IMAGE_PATH, $imagePath);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateDelivery()
    {
        return $this->_getData(AccountInterface::DATE_DELIVERY);
    }

    /**
     * {@inheritdoc}
     */
    public function setDateDelivery($dateDelivery)
    {
        $this->setData(AccountInterface::DATE_DELIVERY, $dateDelivery);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSent()
    {
        return $this->_getData(AccountInterface::IS_SENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSent($isSent)
    {
        $this->setData(AccountInterface::IS_SENT, $isSent);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerCreatedId()
    {
        return $this->_getData(AccountInterface::CUSTOMER_CREATED_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerCreatedId($customerCreatedId)
    {
        $this->setData(AccountInterface::CUSTOMER_CREATED_ID, $customerCreatedId);

        return $this;
    }
}
