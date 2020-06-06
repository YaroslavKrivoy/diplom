<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Block\Adminhtml\Sales\Items\Column\GiftCard;

class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * @var \Amasty\GiftCard\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Amasty\GiftCard\Model\Image
     */
    protected $imageModel;
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Amasty\GiftCard\Helper\Data $dataHelper,
        \Amasty\GiftCard\Model\Image $imageModel,
        \Magento\Store\Model\Store $store,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
        $this->dataHelper = $dataHelper;
        $this->imageModel = $imageModel;
        $this->store = $store;
        $this->orderRepository = $orderRepository;
    }

    public function getOrderOptions()
    {
        return array_merge($this->getGiftCardOptions(), parent::getOrderOptions());
    }

    protected function _prepareCustomOption($code)
    {
        if ($option = $this->getItem()->getProductOptionByCode($code)) {
            return $this->escapeHtml($option);
        }
        return false;
    }

    protected function getGiftCardOptions()
    {
        $result = [];

        $customAmount = $this->_prepareCustomOption('am_giftcard_amount_custom');
        $amount = $this->_prepareCustomOption('am_giftcard_amount');
        $value = $this->dataHelper->getGiftAmount($customAmount, $amount);

        if ($value) {
            $order = $this->orderRepository->get($this->getItem()->getOrderId());
            $orderBaseRate = $order->getBaseToOrderRate();
            $result[] = [
                'label' => __('Card Value'),
                'value' => $this->dataHelper->formatBasePrice($value / $orderBaseRate)
            ];
        }

        $value = $this->_prepareCustomOption('am_giftcard_type');
        $giftcardType = $value;

        if ($value) {
            $result[] = [
                'label' => __('Card Type'),
                'value' => $this->dataHelper->getCardType($value)
            ];
        }

        $value = $this->_prepareCustomOption('am_giftcard_image');
        if ($value) {
            $image = $this->imageModel;
            $image->getResource()->load($image, $value);
            if ($image->getId()) {
                $value = '<img src="'. $image->getImageUrl() .
                         '"  width="270px;" title="'. __('Image Id %1', $image->getId()).'"/>';
                $result[] = [
                    'label' => __('Gift Card Image'),
                    'value' => $value,
                    'custom_view'=> true,
                ];
            }

        }

        $value = $this->_prepareCustomOption('am_giftcard_sender_name');
        if ($value) {
            $email = $this->_prepareCustomOption('am_giftcard_sender_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = [
                'label' => __('Gift Card Sender'),
                'value' => $value
            ];
        }

        $value = $this->_prepareCustomOption('am_giftcard_recipient_name');
        if ($value && $giftcardType != \Amasty\GiftCard\Model\GiftCard::TYPE_PRINTED) {
            $email = $this->_prepareCustomOption('am_giftcard_recipient_email');
            if ($email) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = [
                'label' => __('Gift Card Recipient'),
                'value' => $value
            ];
        }

        $value = $this->_prepareCustomOption('am_giftcard_message');
        if ($value) {
            $result[] = [
                'label' => __('Gift Card Message'),
                'value' => $value
            ];
        }

        if ($value = $this->_prepareCustomOption('am_giftcard_lifetime')) {
            $result[] = [
                'label'=> __('Gift Card Lifetime'),
                'value'=> __('%1 days', $value),
            ];
        }

        if ($value = $this->_prepareCustomOption('am_giftcard_date_delivery')) {
            $result[] = [
                'label'=> __('Date of Certificate Delivery'),
                'value'=> $this->formatDate($value, \IntlDateFormatter::SHORT, true),
            ];
        }

        if ($value = $this->_prepareCustomOption('am_giftcard_date_delivery_timezone')) {
            $result[] = [
                'label'=> __('Delivery Timezone'),
                'value'=> $value,
            ];
        }

        $createdCodes = 0;
        $totalCodes = $this->getItem()->getQtyOrdered();
        if ($codes = $this->getItem()->getProductOptionByCode('am_giftcard_created_codes')) {
            $createdCodes = count($codes);
        }

        if (is_array($codes)) {
            foreach ($codes as &$code) {
                if ($code === null) {
                    $code = __('Unable to create.');
                }
            }
        } else {
            $codes = [];
        }

        for ($i = $createdCodes; $i < $totalCodes; $i++) {
            $codes[] = __('N/A');
        }

        $result[] = [
            'label'=> __('Gift Card Accounts'),
            'value'=>implode('<br />', $codes),
            'custom_view'=>true,
        ];

        return $result;
    }
}
