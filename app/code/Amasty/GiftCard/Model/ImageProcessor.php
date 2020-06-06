<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

use Magento\Framework\ImageFactory as FactoryImage;
use Magento\Catalog\Model\Product\Gallery\MimeTypeExtensionMap;
use Magento\Framework\Exception\FileSystemException;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Amasty\GiftCard\Model\Config\Source\Image as ImageConfig;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Amasty\GiftCard\Model\Image as ModelImage;

class ImageProcessor
{
    /**
     * Image size constants
     */
    const WEIDTH = '446';
    const HEIGHT = '217';

    /**
     * @var FactoryImage
     */
    private $imageFactory;

    /**
     * @var MimeTypeExtensionMap
     */
    private $mimeTypeExtensionMap;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var ImageConfig
     */
    private $imageConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ModelImage
     */
    private $modelImage;

    public function __construct(
        FactoryImage $imageFactory,
        MimeTypeExtensionMap $mimeTypeExtensionMap,
        UploaderFactory $uploaderFactory,
        ImageConfig $imageConfig,
        Filesystem $filesystem,
        ModelImage $modelImage
    ) {
        $this->imageFactory = $imageFactory;
        $this->mimeTypeExtensionMap = $mimeTypeExtensionMap;
        $this->uploaderFactory = $uploaderFactory;
        $this->imageConfig = $imageConfig;
        $this->filesystem = $filesystem;
        $this->modelImage = $modelImage;
    }

    /**
     * @param array $file
     *
     * @return mixed
     */
    public function processImageSaving($file)
    {
        return $this->checkFileFormat($file);
    }

    /**
     * @param array $file
     *
     * @return mixed
     * @throws FileSystemException
     */
    private function checkFileFormat($file)
    {
        $imageType = $this->mimeTypeExtensionMap->getMimeTypeExtension($file['type']);

        if (!$imageType) {
            throw new FileSystemException(__('The uploaded file is not an image.'));
        } else {
            return $this->uploadFileAndGetName($file);
        }
    }

    /**
     * @param array $input
     *
     * @return string
     */
    private function uploadFileAndGetName($input)
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $input]);
        $uploader->setAllowedExtensions($this->imageConfig->getAllowedExtensions())
            ->setAllowRenameFiles(true)
            ->setFilesDispersion(false);

        $path = $this->filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath($this->modelImage->imagePath);
        $result = $uploader->save($path, $input['name']);

        $this->resizeImage($result['path'], $uploader->getUploadedFileName());

        return $result['file'];
    }

    /**
     * Resize image to 446x217
     *
     * @param string $path
     * @param string $imageName
     */
    private function resizeImage($path, $imageName)
    {
        try {
            /** @var \Magento\Framework\Image $imageProcessor */
            $imageProcessor = $this->imageFactory->create(['fileName' => $path . '/' . $imageName]);
            $imageProcessor->constrainOnly(true);
            $imageProcessor->keepTransparency(true);
            $imageProcessor->keepFrame(false);
            $imageProcessor->keepAspectRatio(true);
            $imageProcessor->resize(self::WEIDTH, self::HEIGHT);
            $imageProcessor->save($path . '/' . $imageName);
        } catch (\Exception $e) {
            // Unsupported image format.
        }
    }
}
