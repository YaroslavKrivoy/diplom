<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Owner
 * Date: 16.12.11
 * Time: 10:55
 * To change this template use File | Settings | File Templates.
 */
namespace Infomodus\Upslabel\Model\Config;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Framework\Data\OptionSourceInterface;

class ProductAttributes implements OptionSourceInterface
{
    protected $attribute;

    /**
     * ProductAttributes constructor.
     * @param Collection $attribute
     */
    public function __construct(Collection $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $coll = $this->attribute->setOrder('main_table.frontend_label', 'ASC');
        $attributes = $coll->load()->getItems();
        $attributeArray = [[
            'label' => '',
            'value' => ''
        ]];

        foreach ($attributes as $attribute) {
            $attributeArray[] = [
                'label' => $attribute->getData('frontend_label'),
                'value' => $attribute->getData('attribute_code')
            ];
        }

        return $attributeArray;
    }
}
