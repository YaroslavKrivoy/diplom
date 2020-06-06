<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 *
 * @package BoxPacker
 * @author  Doug Wright
 */

namespace Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;

/**
 * An item to be packed where additional constraints need to be considered. Only implement this interface if you actually
 * need this additional functionality as it will slow down the packing algorithm
 *
 * @author  Doug Wright
 * @package BoxPacker
 */
interface ConstrainedItemInterface extends ItemInterface
{

    /**
     * Hook for user implementation of item-specific constraints, e.g. max <x> batteries per box
     *
     * @param ItemList $alreadyPackedItems
     * @param Box      $box
     *
     * @return bool
     */
    public function canBePackedInBox(ItemList $alreadyPackedItems, BoxInterface $box);
}
