<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config\Pickup;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stores implements OptionSourceInterface
{
    protected $storeManagerInterface;

    /**
     * Stores constructor.
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface
    )
    {
        $this->storeManagerInterface = $storeManagerInterface;
    }

    public function toOptionArray()
    {
        $c = [];
        foreach ($this->storeManagerInterface->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $c[] = ['label' => $store->getName()." (".$website->getName()." \\ ".$group->getName().")", 'value' => $store->getId()];
                }
            }
        }

        return $c;
    }

    public function getStores()
    {
        $c = [];
        foreach ($this->storeManagerInterface->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $c[$store->getId()] = $store->getName()." (".$website->getName()." \\ ".$group->getName().")";
                }
            }
        }
        return $c;
    }
}
