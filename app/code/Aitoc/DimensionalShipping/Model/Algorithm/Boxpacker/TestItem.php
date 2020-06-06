<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 *
 * @package BoxPacker
 * @author  Doug Wright
 */

namespace Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;

class TestItem implements ItemInterface
{

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $weight;

    /**
     * @var int
     */
    private $keepFlat;

    /**
     * @var int
     */
    private $volume;

    /**
     * @var int
     */
    private $orderItemId;

    /**
     * TestItem constructor.
     *
     * @param string $description
     * @param int    $width
     * @param int    $length
     * @param int    $depth
     * @param int    $weight
     * @param int    $keepFlat
     */

    public function __construct($description, $width, $length, $depth, $weight, $keepFlat, $orderItemId)
    {
        $this->description = $description;
        $this->width       = $width;
        $this->length      = $length;
        $this->depth       = $depth;
        $this->weight      = $weight;
        $this->keepFlat    = $keepFlat;
        $this->orderItemId = $orderItemId;

        $this->volume = $this->width * $this->length * $this->depth;
    }

    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @return int
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return int
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @return int
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return int
     */
    public function getKeepFlat()
    {
        return $this->keepFlat;
    }

    /**
     * @return int
     */
    public function setKeepFlat($keepFlat)
    {
        $this->keepFlat = $keepFlat;
    }
}
