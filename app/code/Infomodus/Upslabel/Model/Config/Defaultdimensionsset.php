<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */

namespace Infomodus\Upslabel\Model\Config;

use Infomodus\Upslabel\Model\ResourceModel\Boxes\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Defaultdimensionsset implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * Defaultdimensionsset constructor.
     * @param Collection $collection
     */
    public function __construct(
        CollectionFactory $collection
    )
    {
        $this->collection = $collection;
    }

    public function toOptionArray()
    {

        $collection = $this->collection->create();
        $c = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                if ($item->getEnable() == 1) {
                    $c[] = ['label' => $item->getName(), 'value' => $item->getId()];
                }
            }
        }

        return $c;
    }

    public function getDimensionSets()
    {
        $collection = $this->collection->create();
        $c = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                if ($item->getEnable() == 1) {
                    $c[$item->getId()] = $item->getName();
                }
            }
        }

        return $c;
    }

    public function toOptionObjects()
    {
        $collection = $this->collection->create();
        $c = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                if ($item->getEnable() == 1) {
                    $c[$item->getId()] = $item;
                }
            }
        }

        return $c;
    }
}
