<?php 
namespace Webfitters\Custom\Block;

class RecipeBlock extends \Mageplaza\Blog\Block\Post\Relatedpost {

	protected $recipe;

    public function getRecipe(){
    	if(!$this->recipe && $this->recipe !== false){
    		$collection = $this->helperData->getPostList();
            $collection->getSelect()
                ->join([
                    'related' => $collection->getTable('mageplaza_blog_post_product')],
                    'related.post_id=main_table.post_id AND related.entity_id=' . $this->getProductId()
                )
                ->limit(1)->orderRand();
            if($collection->getSize()==0){
            	$this->recipe = false;
            	return false;
            }
            foreach($collection as $recipe){
            	$this->recipe = $recipe;
            }
    	}
    	return $this->recipe;
    }

    public function getImageUrl($image) {
        return $this->helperData->getImageHelper()->getMediaUrl($image);
    }

    public function setTabTitle(){
    	if(!$this->recipe && $this->recipe !== false){
    		$this->getRecipe();
    	}
        if($this->recipe){
            $this->setTitle('Recipe: '.$this->recipe->getName());
        }
    }

}