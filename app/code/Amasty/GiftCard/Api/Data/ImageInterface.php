<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_GiftCard
 */


namespace Amasty\GiftCard\Api\Data;

interface ImageInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const IMAGE_ID = 'image_id';
    const TITLE = 'title';
    const ACTIVE = 'active';
    const CODE_POS_X = 'code_pos_x';
    const CODE_POS_Y = 'code_pos_y';
    const IMAGE_PATH = 'image_path';
    /**#@-*/

    /**
     * @return int
     */
    public function getImageId();

    /**
     * @param int $imageId
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setImageId($imageId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setTitle($title);

    /**
     * @return int
     */
    public function getActive();

    /**
     * @param int $active
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setActive($active);

    /**
     * @return string
     */
    public function getCodePosX();

    /**
     * @param string $codePosX
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setCodePosX($codePosX);

    /**
     * @return string
     */
    public function getCodePosY();

    /**
     * @param string $codePosY
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setCodePosY($codePosY);

    /**
     * @return string|null
     */
    public function getImagePath();

    /**
     * @param string|null $imagePath
     *
     * @return \Amasty\GiftCard\Api\Data\ImageInterface
     */
    public function setImagePath($imagePath);

    /**
     * @param string|null $imageName
     *
     * @return string|null
     */
    public function getImagePathByName($imageName);
}
