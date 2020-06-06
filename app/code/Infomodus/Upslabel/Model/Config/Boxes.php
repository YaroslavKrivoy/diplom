<?php
namespace Infomodus\Upslabel\Model\Config;

use Infomodus\Upslabel\Model\ResourceModel\Boxes\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Boxes implements OptionSourceInterface
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
        $storeId = null;
        $collection = $this->collection->create();
        $c = [['label' => __('--PLEASE SELECT--'), 'value' => '']];

        if($collection->getSize() > 0) {
            foreach ($collection as $item) {
                if ($item->getEnable() == 1) {
                    $c[] = ['label' => $item->getName(),
                        'value' => $item->getOuterWidth() . 'x'
                            . $item->getOuterHeight() . 'x'
                            . $item->getOuterLengths()
                    ];
                }
            }
        }

        return $c;
    }
}