<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */

namespace Amasty\GiftCard\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Amasty\GiftCard\Api\Data\CodeSetInterface;
use Amasty\GiftCard\Model\ResourceModel\CodeSet;
use Amasty\Base\Setup\SerializedFieldDataConverter;

/**
 * Recurring Data script
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var SerializedFieldDataConverter
     */
    private $serializedFieldDataConverter;

    public function __construct(
        ProductMetadataInterface $productMetadata,
        SerializedFieldDataConverter $serializedFieldDataConverter
    ) {
        $this->productMetadata = $productMetadata;
        $this->serializedFieldDataConverter = $serializedFieldDataConverter;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2', '>=')) {
            $this->serializedFieldDataConverter->convertSerializedDataToJson(
                $setup->getTable(CodeSet::TABLE),
                CodeSetInterface::CODE_SET_ID,
                CodeSetInterface::CONDITIONS_SERIALIZED
            );
        }
    }
}
