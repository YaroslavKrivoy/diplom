<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rudjuk Vitalij
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */

namespace Infomodus\Upslabel\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Infomodus\Upslabel\Model\ResourceModel\Address\CollectionFactory;

class Defaultaddress implements OptionSourceInterface
{
    /**
     * @var \Infomodus\Upslabel\Model\ResourceModel\Address\Collection
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
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $c[] = ['label' => $item->getName(), 'value' => $item->getId()];
            }
        }

        return $c;
    }

    public function getAddresses()
    {
        $collection = $this->collection->create();
        $c = [];
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $c[$item->getId()] = $item->getName();
            }
        }

        return $c;
    }

    /**
     * @param $id
     * @return \Magento\Framework\DataObject
     */
    public function getAddressesById($id)
    {
        $collection = $this->collection->create();
        return $collection->addFieldToFilter('address_id', $id)->load()->getFirstItem();
    }

    public function toOptionObjects()
    {
        $collection = $this->collection->create();
        $c = [];
        if ($collection->count() > 0) {
            foreach ($collection as $item) {
                $c[$item->getId()] = $item;
            }
        }

        return $c;
    }
}
