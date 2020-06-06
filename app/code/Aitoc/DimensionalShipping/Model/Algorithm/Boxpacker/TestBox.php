<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 *
 * @package BoxPacker
 * @author  Doug Wright
 */

namespace Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;

class TestBox implements BoxInterface
{

    /**
     * @var string
     */
    private $reference;

    /**
     * @var int
     */
    private $outerWidth;

    /**
     * @var int
     */
    private $outerLength;

    /**
     * @var int
     */
    private $outerDepth;

    /**
     * @var int
     */
    private $emptyWeight;

    /**
     * @var int
     */
    private $innerWidth;

    /**
     * @var int
     */
    private $innerLength;

    /**
     * @var int
     */
    private $innerDepth;

    /**
     * @var int
     */
    private $maxWeight;

    /**
     * @var int
     */
    private $innerVolume;

    /**
     * @var int
     */
    private $boxId;

    public function __construct(
        $reference,
        $outerWidth,
        $outerLength,
        $outerDepth,
        $emptyWeight,
        $innerWidth,
        $innerLength,
        $innerDepth,
        $maxWeight,
        $boxId,
        $weights
    ) {
        $this->reference   = $reference;
        $this->outerWidth  = $outerWidth;
        $this->outerLength = $outerLength;
        $this->outerDepth  = $outerDepth;
        $this->emptyWeight = $emptyWeight;
        $this->innerWidth  = $innerWidth;
        $this->innerLength = $innerLength;
        $this->innerDepth  = $innerDepth;
        $this->maxWeight   = $maxWeight;
        $this->innerVolume = $this->innerWidth * $this->innerLength * $this->innerDepth;
        $this->boxId       = $boxId;
        $this->weights = $weights;
    }

    /**
     * @return int
     */
    public function getBoxId()
    {
        return $this->boxId;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return int
     */
    public function getOuterWidth()
    {
        return $this->outerWidth;
    }

    /**
     * @return int
     */
    public function setOuterWidth($width)
    {
        $this->outerWidth = $width;
    }

    /**
     * @return int
     */
    public function getOuterLength()
    {
        return $this->outerLength;
    }

    /**
     * @return int
     */
    public function setOuterLength($length)
    {
        $this->outerLength = $length;
    }

    /**
     * @return int
     */
    public function getOuterDepth()
    {
        return $this->outerDepth;
    }

    /**
     * @return int
     */
    public function setOuterDepth($depth)
    {
        $this->outerDepth = $depth;
    }

    /**
     * @return int
     */
    public function getEmptyWeight()
    {
        return $this->emptyWeight;
    }

    /**
     * @return int
     */
    public function setEmptyWeight($weight)
    {
        $this->emptyWeight = $weight;
    }

    /**
     * @return int
     */
    public function getInnerWidth()
    {
        return $this->innerWidth;
    }

    /**
     * @return int
     */
    public function setInnerWidth($width)
    {
        $this->innerWidth = $width;
    }

    /**
     * @return int
     */
    public function getInnerLength()
    {
        return $this->innerLength;
    }

    /**
     * @return int
     */
    public function setInnerLength($length)
    {
        $this->innerLength = $length;
    }

    /**
     * @return int
     */
    public function getInnerDepth()
    {
        return $this->innerDepth;
    }

    /**
     * @return int
     */
    public function setInnerDepth($depth)
    {
        $this->innerDepth = $depth;
    }

    /**
     * @return int
     */
    public function getInnerVolume()
    {
        return $this->innerVolume;
    }

    /**
     * @return int
     */
    public function setInnerVolume($volume)
    {
        $this->innerVolume = $volume;
    }

    /**
     * @return int
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * @return int
     */
    public function setMaxWeight($weight)
    {
        $this->maxWeight = $weight;
    }
    protected $weights;
    public function setWeights($weights){
        $this->weights = $weights;
    }
    public function getWeights(){
        return $this->weights;
    }
}
