<?php
/**
 * Mageplaza_Affiliate extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the Mageplaza License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     https://www.mageplaza.com/LICENSE.txt
 * 
 *                     @category  Mageplaza
 *                     @package   Mageplaza_Affiliate
 *                     @copyright Copyright (c) 2016
 *                     @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Affiliate\Model\Account;

/**
 * Class Group
 * @package Mageplaza\Affiliate\Model\Account
 */
class Group implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @type \Mageplaza\Affiliate\Model\GroupFactory
     */
    protected $group;

    public function __construct(
        \Mageplaza\Affiliate\Model\GroupFactory $groupFactory
    )
    {
        $this->group = $groupFactory;
    }


    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $group   = $this->getGroupCollection();
        $options = array();
        foreach ($group as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName()
            ];
        }

        return $options;
    }

    public function getOptionHash(){
        foreach($this->getGroupCollection() as $group){

        }
    }

    public function getGroupCollection()
    {
        $groupModel = $this->group->create();

        return $groupModel->getCollection();
    }
}
