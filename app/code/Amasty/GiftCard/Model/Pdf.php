<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Model;

class Pdf
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $manager;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $manager
    ) {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->manager = $manager;
    }

    /**
     * @param $imageString
     * @return bool|string
     */
    public function create($imageString)
    {
        try {
            return $this->createPdfPageFromImageString($imageString);
        } catch (\Exception $e) {
            $this->manager->addErrorMessage(__('Something went wrong. Please review the error log.'));
            $this->logger->critical($e->getMessage());
        }

        return true;
    }

    /**
     * @param $imageString
     * @return bool|string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Pdf_Exception
     */
    private function createPdfPageFromImageString($imageString)
    {
        /** @var \Magento\Framework\Filesystem\Directory\Write $directory */
        $directory = $this->filesystem->getDirectoryWrite(
            \Magento\Framework\App\Filesystem\DirectoryList::TMP
        );
        $directory->create();

        $ext = pathinfo($imageString, PATHINFO_EXTENSION);
        $image = null;

        if ($ext === 'png') {
            $image = @imagecreatefrompng($imageString);
        } elseif ($ext === 'jpg' || $ext === 'jpeg') {
            $image = @imagecreatefromjpeg($imageString);
        }

        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);

        $pdf = new \Zend_Pdf();
        $pdf->pages[] = $pdf->newPage($xSize, $ySize);
        /** @var \Zend_Pdf_Page $page */
        $page = $pdf->pages[0];

        imageinterlace($image, 0);
        $tmpFileName = $directory->getAbsolutePath(
            'amasty_gift_card' . uniqid(\Magento\Framework\Math\Random::getRandomNumber()) . time() . '.png'
        );
        imagepng($image, $tmpFileName);
        $pdfImage = \Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        $directory->delete($directory->getRelativePath($tmpFileName));
        if (is_resource($image)) {
            imagedestroy($image);
        }

        return $pdf->render();
    }
}
