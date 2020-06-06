<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Product\Type;

use Amasty\GiftCard\Model\GiftCard as ModelGiftCard;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Amasty\GiftCard\Model\ImageFactory;
use Amasty\GiftCard\Model\ResourceModel\Image;

class GiftCard extends \Magento\Catalog\Model\Product\Type\Simple
{
    const TYPE_GIFTCARD_PRODUCT = 'amgiftcard';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Image\Collection
     */
    protected $imageCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializerBase;

    /**
     * @var \Amasty\GiftCard\Model\ConfigModel
     */
    private $configModel;
    /**
     * @var \Amasty\GiftCard\Block\Product\View\Type\GiftCard
     */
    private $cardBlock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Amasty\GiftCard\Model\ImageFactory
     */
    private $imageFactory;

    /**
     * @var \Amasty\GiftCard\Model\ResourceModel\Image
     */
    private $resourceImage;

    /**
     * @var \Amasty\GiftCard\Model\ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        ProductRepositoryInterface $productRepository,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Magento\Catalog\Model\Product $productModel,
        \Amasty\GiftCard\Model\ResourceModel\Image\Collection $imageCollection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Amasty\GiftCard\Model\ConfigModel $configModel,
        PriceCurrencyInterface $priceCurrency,
        \Amasty\Base\Model\Serializer $serializerBase,
        \Amasty\GiftCard\Block\Product\View\Type\GiftCard $cardBlock,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        ImageFactory $imageFactory,
        Image $resourceImage,
        \Amasty\GiftCard\Model\ImageProcessor $imageProcessor
    ) {
        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $context->getEventManager(),
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $context->getLogger(),
            $productRepository
        );
        $this->productModel = $productModel;
        $this->dataHelper = $dataHelper;
        $this->imageCollection = $imageCollection;
        $this->scopeConfig = $context->getScopeConfig();
        $this->messageManager = $messageManager;
        $this->date = $date;
        $this->priceCurrency = $priceCurrency;
        $this->serializerBase = $serializerBase;
        $this->productRepository = $productRepository;
        $this->configModel = $configModel;
        $this->cardBlock = $cardBlock;
        $this->request = $context->getRequest();
        $this->adapterFactory = $adapterFactory;
        $this->imageFactory = $imageFactory;
        $this->resourceImage = $resourceImage;
        $this->imageProcessor = $imageProcessor;
    }

    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $isExistFile = count($this->request->getFiles());
        if ($isExistFile && !$buyRequest->getAmGiftcardImage()) {
            $file = $this->request->getFiles('amgiftcard-userimage-input');
            $image = $this->imageProcessor->processImageSaving($file);

            $model = $this->imageFactory->create();
            $imageData = [
                'title' => 'Client Image',
                'active' => 0,
                'image_path' => $image
            ];
            $model->addData($imageData);
            $this->resourceImage->save($model);
            /** @var $buyRequest DataObject */
            $buyRequest->setAmGiftcardImage($model->getImageId());
        }

        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        try {
            $amount = $this->_validate($buyRequest, $product, $processMode);
        } catch (\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return __('An error has occurred while preparing Gift Card.');
        }

        $product->addCustomOption('am_giftcard_amount', $amount, $product);

        foreach ($this->_customFields() as $field => $data) {
            if ($field == 'am_giftcard_amount') {
                continue;
            }
            if ($field == 'am_giftcard_type'
                && $product->getAmGiftcardType() != ModelGiftCard::TYPE_COMBINED
            ) {
                $product->addCustomOption($field, $product->getAmGiftcardType(), $product);
                continue;
            }

            if ($field == 'am_giftcard_date_delivery') {
                $currentDate = strtotime($buyRequest->getData('am_giftcard_date_delivery')
                    . " " . $buyRequest->getData('am_giftcard_date_delivery_timezone'));
                $date = $this->date->gmtDate(null, $currentDate);
                $product->addCustomOption($field, $date, $product);
                continue;
            }
            $product->addCustomOption($field, $buyRequest->getData($field), $product);
        }

        return $result;

    }

    public function canConfigure($product){
        return true;
    }


    private function isProductHasOpenAmount($currentProduct, $buyRequest, $allowedAmounts)
    {
        return $currentProduct->getAmAllowOpenAmount()
            && ($buyRequest->getAmGiftcardAmount() == 'custom' || count($allowedAmounts) == 0);
    }

    /**
     * @param DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param $processMode
     *
     * @return float|mixed|null
     *
     * @throws LocalizedException
     */
    private function _validate(DataObject $buyRequest, $product, $processMode)
    {
        $currentProduct = $this->productRepository->getById($product->getId());

        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);
        $minCustomAmount = $currentProduct->getAmOpenAmountMin();
        $maxCustomAmount = $currentProduct->getAmOpenAmountMax();

        $allowedAmounts = $this->getAllowedAmounts($currentProduct);
        $isAmountCustom = $this->isProductHasOpenAmount($currentProduct, $buyRequest, $allowedAmounts);

        if ($isStrictProcessMode) {
            if ($currentProduct->getTypeId() == self::TYPE_GIFTCARD_PRODUCT
                && $currentProduct->getAmGiftcardType() == ModelGiftCard::TYPE_COMBINED
                && !empty($buyRequest->getAmGiftcardType())
            ) {
                $currentProduct->setAmGiftcardType($buyRequest->getAmGiftcardType());
            }
            $giftType = $currentProduct->getAmGiftcardType();

            $listImages = $this->getImages($currentProduct);

            $listFields = $this->_customFields();
            $listFields = $this->validateField(
                $allowedAmounts,
                $isAmountCustom,
                $listImages,
                $giftType,
                $buyRequest,
                $listFields
            );

            $listErrors = $this->getListErrors($listFields, $buyRequest);
            $this->throwErrors($listErrors);
        }

        $amount = null;
        if ($isAmountCustom) {
            $amGiftCardAmountCustom = $buyRequest->getAmGiftcardAmountCustom();
            $minCustomAmountConverted = $this->priceCurrency->convertAndRound($minCustomAmount);
            $maxCustomAmountConverted = $this->priceCurrency->convertAndRound($maxCustomAmount);
            $this->validateAmount(
                $minCustomAmountConverted,
                $maxCustomAmountConverted,
                $amGiftCardAmountCustom,
                $isStrictProcessMode
            );

            if ($this->isValidGiftCardAmount($minCustomAmount, $maxCustomAmount, $amGiftCardAmountCustom)) {
                $amount = $amGiftCardAmountCustom;
            }
        } else {
            $buyRequestAmount = $buyRequest->getAmGiftcardAmount();
            if (count($allowedAmounts) == 1) {
                $amount = array_shift($allowedAmounts);
            } elseif (isset($allowedAmounts[(string)$this->priceCurrency->round($buyRequestAmount)])) {
                $amount = $allowedAmounts[(string)$this->priceCurrency->round($buyRequestAmount)];
            }
        }

        return $amount;
    }

    /**
     * @param $minCustomAmount
     * @param $maxCustomAmount
     * @param $amGiftCardAmountCustom
     *
     * @return bool
     */
    private function isValidGiftCardAmount($minCustomAmount, $maxCustomAmount, $amGiftCardAmountCustom)
    {
        return (!$minCustomAmount || $minCustomAmount <= $amGiftCardAmountCustom)
            && (!$maxCustomAmount || $maxCustomAmount >= $amGiftCardAmountCustom)
            && $amGiftCardAmountCustom > 0;
    }

    /**
     * @param $minCustomAmountConverted
     * @param $maxCustomAmountConverted
     * @param $amGiftCardAmountCustom
     * @param $isStrictProcessMode
     *
     * @throws LocalizedException
     */
    private function validateAmount(
        $minCustomAmountConverted,
        $maxCustomAmountConverted,
        $amGiftCardAmountCustom,
        $isStrictProcessMode
    ) {
        if ($minCustomAmountConverted
            && $minCustomAmountConverted > $amGiftCardAmountCustom
            && $isStrictProcessMode
        ) {
            throw new LocalizedException(
                __('Gift Card min amount is %1', $minCustomAmountConverted)
            );
        }

        if ($maxCustomAmountConverted && $maxCustomAmountConverted < $amGiftCardAmountCustom && $isStrictProcessMode) {
            $maxCustomAmountText = $maxCustomAmountConverted;
            throw new LocalizedException(
                __('Gift Card max amount is %1', $maxCustomAmountText)
            );
        }

        if ($amGiftCardAmountCustom <= 0 && $isStrictProcessMode) {
            throw new LocalizedException(
                __('Please specify Gift Card Value')
            );
        }
    }

    /**
     * @param array $listErrors
     * @throws LocalizedException
     */
    private function throwErrors($listErrors)
    {
        $countErrors = count($listErrors);
        if ($countErrors > 1) {
            throw new LocalizedException(
                __('Please specify all the required information.')
            );
        } elseif ($countErrors) {
            throw new LocalizedException(
                $listErrors[0]
            );
        }
    }

    /**
     * @param array $listFields
     * @param DataObject $buyRequest
     *
     * @return array
     */
    private function getListErrors($listFields, $buyRequest)
    {
        $listErrors = [];
        foreach ($listFields as $field => $data) {
            $isCheck = isset($data['isCheck']) ? $data['isCheck'] : true;
            if (!$buyRequest->getData($field) && $isCheck) {
                $listErrors[] = __('Please specify %1', $data['fieldName']);
            }
        }

        return $listErrors;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $currentProduct
     * @return array
     */
    private function getAllowedAmounts($currentProduct)
    {
        $allowedAmounts = [];
        foreach ($currentProduct->getPriceModel()->getAmounts($currentProduct) as $value) {
            $itemAmount = (string)$this->priceCurrency->round($value['website_value']);
            $allowedAmounts[$itemAmount] = $itemAmount;
        }

        return $allowedAmounts;
    }

    /**
     * @param $allowedAmounts
     * @param $isAmountCustom
     * @param $listImages
     * @param $giftType
     * @param DataObject $buyRequest
     * @param $listFields
     *
     * @return array
     */
    private function validateField($allowedAmounts, $isAmountCustom, $listImages, $giftType, $buyRequest, $listFields)
    {
        $allowedOptions = $this->cardBlock->getAllowedOptions();
        $listFields['am_giftcard_amount']['isCheck'] = !(count($allowedAmounts) == 1) && !$isAmountCustom;
        $listFields['am_giftcard_amount_custom']['isCheck'] = $isAmountCustom;
        $listFields['am_giftcard_image']['isCheck'] = (bool)$listImages;
        $listFields['am_giftcard_type']['isCheck'] = $giftType == ModelGiftCard::TYPE_COMBINED;
        if (!strstr($allowedOptions, \Amasty\GiftCard\Model\Options::GIFT_DATE_DELIVERY)) {
            $listFields['am_giftcard_date_delivery']['isCheck'] = false;
            $listFields['am_giftcard_date_delivery_timezone']['isCheck'] = false;
        }

        if ($this->validateProductByType($giftType, $buyRequest)) {
            $listFields['am_giftcard_recipient_name']['isCheck'] = false;
            $listFields['am_giftcard_recipient_email']['isCheck'] = false;
        }

        if (!strstr($allowedOptions, \Amasty\GiftCard\Model\Options::GIFT_RECIPIENT_NAME)) {
            $listFields['am_giftcard_recipient_name']['isCheck'] = false;
        }

        if (!strstr($allowedOptions, \Amasty\GiftCard\Model\Options::GIFT_SENDER_NAME)) {
            $listFields['am_giftcard_sender_name']['isCheck'] = false;
        }

        if (!strstr($allowedOptions, \Amasty\GiftCard\Model\Options::GIFT_SENDER_EMAIL)) {
            $listFields['am_giftcard_sender_email']['isCheck'] = false;
        }

        return $listFields;
    }

    /**
     * @param $giftType
     * @param DataObject $buyRequest
     * @return bool
     */
    private function validateProductByType($giftType, $buyRequest)
    {
        return ($giftType == ModelGiftCard::TYPE_COMBINED
                && $buyRequest->getData('am_giftcard_type') == ModelGiftCard::TYPE_PRINTED)
            || $giftType == ModelGiftCard::TYPE_PRINTED;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Amasty\GiftCard\Model\ResourceModel\Image\Collection
     */
    public function getImages($product)
    {
        $imageIds = $product->getAmGiftcardCodeImage();
        $this->imageCollection
            ->addFieldToFilter('image_id', ['in' => $imageIds])
            ->addFieldToFilter('active', \Amasty\GiftCard\Model\Image::STATUS_ACTIVE);

        return $this->imageCollection;
    }

    protected function _customFields()
    {
        return $this->dataHelper->getAmGiftCardFields();
    }

    /**
     * @param \Magento\Catalog\Model\Product|null $product
     * @return $this
     */
    public function checkProductBuyState($product = null)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
            $buyRequest = new DataObject($this->serializerBase->unserialize($option->getValue()));
            $this->_validate($buyRequest, $product, self::PROCESS_MODE_FULL);
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        try {
            $product = $this->productRepository->getById($product->getId());
        } catch (LocalizedException $ex) {
            $product = null;
        }

        if ($product) {
            $isVirtualGiftCard = $product->getTypeId() === self::TYPE_GIFTCARD_PRODUCT
                && (int)$product->getAmGiftcardType() === ModelGiftCard::TYPE_VIRTUAL;

            return $isVirtualGiftCard;
        }

        return false;
    }
}
