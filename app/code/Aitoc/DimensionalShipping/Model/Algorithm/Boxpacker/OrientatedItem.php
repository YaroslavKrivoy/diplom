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
class OrientatedItem
{

    /**
     * @var Item
     */
    protected $item;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var int
     */
    protected $depth;

    /**
     * Constructor.
     *
     * @param Item $item
     * @param int  $width
     * @param int  $length
     * @param int  $depth
     */
    public function __construct(ItemInterface $item, $width, $length, $depth)
    {
        $this->item   = $item;
        $this->width  = $width;
        $this->length = $length;
        $this->depth  = $depth;
    }

    /**
     * Item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Is this orientation stable (low centre of gravity)
     * N.B. Assumes equal weight distribution
     *
     * @return bool
     */
    public function isStable()
    {
        return $this->getDepth() <= min($this->getLength(), $this->getWidth());
    }

    /**
     * Item depth in mm in it's packed orientation
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Item length in mm in it's packed orientation
     *
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Item width in mm in it's packed orientation
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
}
