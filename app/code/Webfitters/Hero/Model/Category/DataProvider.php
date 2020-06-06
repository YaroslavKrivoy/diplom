<?php
namespace Webfitters\Hero\Model\Category;
  
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
  
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'hero_image'; // custom image field
         
        return $fields;
    }
}