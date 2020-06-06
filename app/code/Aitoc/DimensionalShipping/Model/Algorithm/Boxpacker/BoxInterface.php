<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 *
 * @package BoxPacker
 * @author  Doug Wright
 */

namespace Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;

/**
 * A "box" (or envelope?) to pack items into
 *
 * @author  Doug Wright
 * @package BoxPacker
 */
interface BoxInterface
{


    public function getBoxId();

    /**
     * Reference for box type (e.g. SKU or description)
     *
     * @return string
     */
    public function getReference();

    /**
     * Outer width in mm
     *
     * @return int
     */
    public function getOuterWidth();

    /**
     * Outer length in mm
     *
     * @return int
     */
    public function getOuterLength();

    /**
     * Outer depth in mm
     *
     * @return int
     */
    public function getOuterDepth();

    /**
     * Empty weight in g
     *
     * @return int
     */
    public function getEmptyWeight();

    /**
     * Inner width in mm
     *
     * @return int
     */
    public function getInnerWidth();

    /**
     * Inner length in mm
     *
     * @return int
     */
    public function getInnerLength();

    /**
     * Inner depth in mm
     *
     * @return int
     */
    public function getInnerDepth();

    /**
     * Total inner volume of packing in mm^3
     *
     * @return int
     */
    public function getInnerVolume();

    /**
     * Max weight the packaging can hold in g
     *
     * @return int
     */
    public function getMaxWeight();

    /**
     * Reference for box type (e.g. SKU or description)
     *
     * @return string
     */
    public function setReference($reference);

    /**
     * Outer width in mm
     *
     * @return int
     */
    public function setOuterWidth($width);

    /**
     * Outer length in mm
     *
     * @return int
     */
    public function setOuterLength($length);

    /**
     * Outer depth in mm
     *
     * @return int
     */
    public function setOuterDepth($depth);

    /**
     * Empty weight in g
     *
     * @return int
     */
    public function setEmptyWeight($weight);

    /**
     * Inner width in mm
     *
     * @return int
     */
    public function setInnerWidth($width);

    /**
     * Inner length in mm
     *
     * @return int
     */
    public function setInnerLength($length);

    /**
     * Inner depth in mm
     *
     * @return int
     */
    public function setInnerDepth($depth);

    /**
     * Total inner volume of packing in mm^3
     *
     * @return int
     */
    public function setInnerVolume($volume);

    /**
     * Max weight the packaging can hold in g
     *
     * @return int
     */
    public function setMaxWeight($weight);

    public function setWeights($weights);
    public function getWeights();
}
