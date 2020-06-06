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
namespace Mageplaza\Affiliate\Model\Banner;
use Magento\Cms\Model\BlockFactory;

class Block implements \Magento\Framework\Option\ArrayInterface
{
    protected $_cms;
    protected $_options;

    public function __construct(
        BlockFactory $blockFactory
    )
    {
        $this->_cms = $blockFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $cmsBlock           = $this->_cms->create();
        $cmsBlockCollection = $cmsBlock->getCollection();
        if (!$this->_options) {
            foreach ($cmsBlockCollection as $item) {
                $this->_options[] = array(
                    'label' => $item->getData('title'),
                    'value' => $item->getData('identifier')
                );
            }
        }


        return $this->_options;
    }
}
