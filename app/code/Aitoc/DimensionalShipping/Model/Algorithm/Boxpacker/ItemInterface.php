<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 *
 * @package BoxPacker
 * @author  Doug Wright
 */

namespace Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;

/**
 * An item to be packed
 *
 * @author  Doug Wright
 * @package BoxPacker
 */
interface ItemInterface
{
    /**
     * Item SKU etc
     *
     * @return string
     */
    public function getDescription();

    /**
     * Item width in mm
     *
     * @return int
     */
    public function getWidth();

    /**
     * Item length in mm
     *
     * @return int
     */
    public function getLength();

    /**
     * Item depth in mm
     *
     * @return int
     */
    public function getDepth();

    /**
     * Item weight in g
     *
     * @return int
     */
    public function getWeight();

    /**
     * Item volume in mm^3
     *
     * @return int
     */
    public function getVolume();

    /**
     * Does this item need to be kept flat?
     *
     * @return bool
     */
    public function getKeepFlat();


    /**
     * Item SKU etc
     *
     * @return string
     */
    public function setDescription($description);

    /**
     * Item width in mm
     *
     * @return int
     */
    public function setWidth($width);

    /**
     * Item length in mm
     *
     * @return int
     */
    public function setLength($length);

    /**
     * Item depth in mm
     *
     * @return int
     */
    public function setDepth($depth);

    /**
     * Item weight in g
     *
     * @return int
     */
    public function setWeight($weight);

    /**
     * Item volume in mm^3
     *
     * @return int
     */
    public function setVolume($volume);

    /**
     * Does this item need to be kept flat?
     *
     * @return bool
     */
    public function setKeepFlat($keepFlat);
}
