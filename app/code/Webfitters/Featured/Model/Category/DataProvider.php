<?php
namespace Webfitters\Featured\Model\Category;
  
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
  
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'featured';
        return $fields;
    }
}