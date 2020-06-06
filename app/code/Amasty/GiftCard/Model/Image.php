<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Magento\Framework\Model\AbstractModel;

class Image extends AbstractModel implements ImageInterface
{
    const STATUS_ACTIVE		= 1;
    const STATUS_INACTIVE	= 0;

    public $imagePath 	= 'amasty_giftcard/image_templates';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\GiftCard\Model\ResourceModel\Image');
        $this->setIdFieldName('image_id');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\GiftCard\Model\ResourceModel\Image $resource,
        \Amasty\GiftCard\Model\ResourceModel\Image\Collection $resourceCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManager = $storeManager;
    }

    public function getImage()
    {
        return $this->_getData(ImageInterface::IMAGE_PATH);
    }

    public function getMediaDir()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    public function getImageUrl()
    {
        if ($image = $this->_getData(ImageInterface::IMAGE_PATH)) {
            $media = $this->getMediaDir();

            return $media . $this->imagePath . DIRECTORY_SEPARATOR . $image;
        } else {
            return '';
        }
    }

    public function getListStatuses()
    {
        return array(
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_ACTIVE => __('Active')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getImageId()
    {
        return $this->_getData(ImageInterface::IMAGE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageId($imageId)
    {
        $this->setData(ImageInterface::IMAGE_ID, $imageId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_getData(ImageInterface::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(ImageInterface::TITLE, $title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive()
    {
        return $this->_getData(ImageInterface::ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setActive($active)
    {
        $this->setData(ImageInterface::ACTIVE, $active);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodePosX()
    {
        return $this->_getData(ImageInterface::CODE_POS_X);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodePosX($codePosX)
    {
        $this->setData(ImageInterface::CODE_POS_X, $codePosX);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodePosY()
    {
        return $this->_getData(ImageInterface::CODE_POS_Y);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodePosY($codePosY)
    {
        $this->setData(ImageInterface::CODE_POS_Y, $codePosY);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagePath()
    {
        if ($image = $this->_getData(ImageInterface::IMAGE_PATH)) {
            return $this->collectPath($image);
        } else {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setImagePath($imagePath)
    {
        $this->setData(ImageInterface::IMAGE_PATH, $imagePath);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImagePathByName($imageName)
    {
        if ($imageName) {
            return $this->collectPath($imageName);
        } else {
            return '';
        }
    }

    /**
     * @param string $imageName
     *
     * @return string
     */
    public function collectPath($imageName)
    {
        $DS = DIRECTORY_SEPARATOR;
        $media = $this->getMediaDir();
        if (substr($media, -1) != $DS) {
            $media .= $DS;
        }

        return $media . $this->imagePath . $DS . $imageName;
    }
}
