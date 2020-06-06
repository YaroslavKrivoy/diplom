<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model\Repository;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Api\ImageRepositoryInterface;
use Amasty\GiftCard\Model\ImageFactory;
use Amasty\GiftCard\Model\ResourceModel\Image;
use Amasty\GiftCard\Model\ResourceModel\Image\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ImageRepository implements ImageRepositoryInterface
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var Image
     */
    private $imageResource;

    /**
     * @var array
     */
    private $images;

    /**
     * @var CollectionFactory
     */
    private $imageCollectionFactory;

    public function __construct(
        ImageFactory $imageFactory,
        Image $imageResource,
        CollectionFactory $imageCollectionFactory
    ) {
        $this->imageFactory = $imageFactory;
        $this->imageResource = $imageResource;
        $this->imageCollectionFactory = $imageCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ImageInterface $image)
    {
        try {
            $this->imageResource->save($image);
        } catch (\Exception $e) {
            if ($image->getImageId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save image with ID %1. Error: %2',
                        [$image->getImageId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new image. Error: %1', $e->getMessage()));
        }

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($imageId)
    {
        if (!isset($this->images[$imageId])) {
            /** @var \Amasty\GiftCard\Model\Image $image */
            $image = $this->imageFactory->create();
            $this->imageResource->load($image, $imageId);
            if (!$image->getImageId()) {
                throw new NoSuchEntityException(__('image with specified ID "%1" not found.', $imageId));
            }
            $this->images[$imageId] = $image;
        }

        return $this->images[$imageId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ImageInterface $image)
    {
        try {
            $this->imageResource->delete($image);
            unset($this->images[$image->getImageId()]);
        } catch (\Exception $e) {
            if ($image->getImageId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove image with ID %1. Error: %2',
                        [$image->getImageId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove image. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($imageId)
    {
        $imageModel = $this->getById($imageId);
        $this->delete($imageModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        /** @var \Amasty\GiftCard\Model\ResourceModel\Image\Collection $imageCollection */
        $imageCollection = $this->imageCollectionFactory->create();

        return $imageCollection->getItems();
    }
}
