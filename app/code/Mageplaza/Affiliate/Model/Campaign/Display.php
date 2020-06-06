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
namespace Mageplaza\Affiliate\Model\Campaign;

class Display implements \Magento\Framework\Option\ArrayInterface
{
    const ALLOW_GUEST = 1;
    const AFFILIATE_MEMBER_ONLY = 2;


    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ALLOW_GUEST,
                'label' => __('Allow Guest')
            ],
            [
                'value' => self::AFFILIATE_MEMBER_ONLY,
                'label' => __('Affiliate Member Only')
            ],
        ];
        return $options;

    }
}
