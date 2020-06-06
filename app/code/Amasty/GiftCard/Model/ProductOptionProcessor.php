<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Model\Options as Options;
use Amasty\GiftCard\Model\GiftCardOptionsConverter as GiftCardOptionsConverter;
use Amasty\GiftCard\Api\Data\AmGiftCardOptionsInterface as AmGiftCardOptionsInterface;
use Amasty\GiftCard\Api\Data\AmGiftCardOptionsInterfaceFactory as AmGiftCardOptionsInterfaceFactory;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

class ProductOptionProcessor implements ProductOptionProcessorInterface
{
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * @var AmGiftCardOptionsInterfaceFactory
     */
    private $amGiftCardOptionsInterfaceFactory;

    /**
     * @var GiftCardOptionsConverter
     */
    private $giftCardOptionsConverter;

    public function __construct(
        DataObjectFactory $objectFactory,
        AmGiftCardOptionsInterfaceFactory $amGiftCardOptionsInterfaceFactory,
        GiftCardOptionsConverter $giftCardOptionsConverter
    ) {
        $this->objectFactory = $objectFactory;
        $this->amGiftCardOptionsInterfaceFactory = $amGiftCardOptionsInterfaceFactory;
        $this->giftCardOptionsConverter = $giftCardOptionsConverter;
    }

    /**
     * @param ProductOptionInterface $productOption
     * @return DataObject
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();
        /** @var AmGiftCardOptionsInterface $amgiftcardOptions */
        $amgiftcardOptions = $this->getAmgiftcardOptions($productOption);

        if ($amgiftcardOptions !== null) {
            $requestData = $this->giftCardOptionsConverter->prepareToBuyRequest($amgiftcardOptions);
            $request->addData($requestData);
        }

        return $request;
    }

    /**
     * Retrieve amasty gift card options
     *
     * @param ProductOptionInterface $productOption
     * @return AmGiftCardOptionsInterface|null
     */
    protected function getAmgiftcardOptions(ProductOptionInterface $productOption)
    {
        if ($productOption
            && $productOption->getExtensionAttributes()
            && $productOption->getExtensionAttributes()->getAmgiftcardOptions()
        ) {
            return $productOption->getExtensionAttributes()->getAmgiftcardOptions();
        }

        return null;
    }

    /**
     * @param DataObject $request
     * @return array
     */
    public function convertToProductOption(DataObject $request)
    {
        $requestData = $request->getData();
        /** @var Options $productOption */
        $productOption = $this->amGiftCardOptionsInterfaceFactory->create();
        $this->giftCardOptionsConverter->prepareToProductOption($requestData, $productOption);

        return ['amgiftcard_options' => $productOption];
    }
}
