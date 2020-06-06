<?php
namespace Webfitters\Importer\Console\Commands;

class RecipeCreate extends \Webfitters\Importer\Console\ImporterCommand {

	protected $post;
	protected $dir;
	protected $product;

	public function __construct(
		\Magento\Framework\App\State $state,
		\Magento\Framework\Filesystem\DirectoryList $dir,
		\Magento\Catalog\Model\ProductFactory $product,
		\Mageplaza\Blog\Model\PostFactory $post,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->post = $post;
		$this->dir = $dir;
		$this->product = $product;
	}

	protected function configure(){
		$this->setName('webfitters:recipes')->setDescription('Imports recipes from live database.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$recipes = $this->db->query('SELECT * FROM `gs_recipe`;')->fetchAll(\PDO::FETCH_OBJ);
		$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($recipes));
		foreach($recipes as $recipe){
			$ingredients = $this->db->query('SELECT * FROM `gs_recipe_ingredient` WHERE `recipe_id` = '.$recipe->id.';')->fetchAll(\PDO::FETCH_OBJ);
			$post = $this->post->create()->load($recipe->name, 'name');
			if(!$post->getId()){
				$post = $this->post->create();
			}
			$post->setName($recipe->name);
			$content = 
				'<h4>'.$recipe->name.'</h4>' . 
				'<p class="description"><span class="info">Description:</span> '.$recipe->description.'</p>' .
				'<p class="description"><span class="info">Servings:</span> '.$recipe->servings.'</p>' .
				'<p class="description"><span class="info">Difficulty:</span> '.ucwords($recipe->difficulty).'</p>' .
				'<p class="description"><span class="info">Meal:</span> '.ucwords($recipe->meal).'</p>' .
				'<div class="ingredient-holder"><h5>Ingredients:</h5><ul class="amount">' 

			;
			foreach($ingredients as $ingredient){
				$content.='<li>'.$ingredient->name.'</li>';
			}
			$content .=
				'</ul></div>' .
				'<p><span class="recipe"><span class="recipe"></span></span></p>' .
				'<p class="instructions">' .
				(($recipe->preparation_instructions != '')?'<span class="info">Preparation:</span> '.$recipe->preparation_instructions.'<br/>':'') .
				(($recipe->cooking_instructions != '')?'<span class="info">Cooking:</span> '.$recipe->cooking_instructions.'<br/>':'') .
				(($recipe->serving_instructions != '')?'<span class="info">Serving:</span> '.$recipe->serving_instructions.'<br/>':'') .
				'</p>';
			$post->setPostContent($content);
			$post->setShortDescription($recipe->description);
			$post->setStoreIds(0);
			if($image = $this->getImage($recipe)){
				$post->setImage(str_replace($this->dir->getPath('pub').'/media/', '', $image));
			}
			$post->setEnabled(true);
			$post->setAllowComment(0);
			$post->setMetaTitle($recipe->name);
			$post->setMetaDescription($recipe->description);
			$post->setMetaKeywords($recipe->tags);
			$post->setMetaRobots('INDEX,FOLLOW');
			$post->setAuthorId(1);
			$post->setModifierId(1);
			$post->setCategoriesIds(array(13));
			$post->setPublishDate(date('Y-m-d H:i:s'));
			if($recipe->tags != ''){
				$tags = array_map('trim', explode(',', $recipe->tags));
				if(is_array($tags) && count($tags) > 0){
					$final = array();
					$query = 'SELECT * FROM `gs_product` WHERE ';
					foreach($tags as $tag){
						$query.='`tags` LIKE "%'.$tag.'%" OR ';
					}	
					$query = rtrim($query, ' OR ');
					$products = $this->db->query($query)->fetchAll(\PDO::FETCH_OBJ);
					foreach($products as $product){
						$p = $this->product->create()->loadByAttribute('sku', $product->number);
						if($p){
							$final[$p->getId()] = 1;
						}
					}
					$post->setProductsData($final);
				}
			}
			$post->save();
			$progress->advance();
		}
		$progress->finish();
	}

	private function getImage($recipe){
		$temp = $this->dir->getPath('pub').'/media/mageplaza/blog/imported';
		if(!file_exists($temp)){
			mkdir($temp);
		}
		if(!file_exists($temp.'/'.$recipe->id)){
			mkdir($temp.'/'.$recipe->id);
		}
		if($recipe->image == ''){
			return null;
		}
		if(file_exists($temp.'/'.$recipe->id.'/'.$recipe->image)){
			return $temp.'/'.$recipe->id.'/'.$recipe->image;
		}
		$bucket = intval(intval($recipe->id)/256)+1;
		$file = @file_get_contents('https://blackwing.com/images/recipes/'.$bucket.'/'.$recipe->id.'/'.rawurlencode($recipe->image));
		if(!$file){
			return null;
		}
		$image = fopen($temp.'/'.$recipe->id.'/'.$recipe->image, 'w+');
		fwrite($image, $file);
		fclose($image);
		/*$i = new \Imagick($temp.'/'.$product->id.'/'.$info->image);
		$dim = $i->getImageGeometry();
		if($dim['width'] < 550 && $dim['height'] < 550){
			$i->resizeImage(550, 550, \Imagick::FILTER_LANCZOS, 0.95, true);
			$i->writeImage($temp.'/'.$product->id.'/'.$info->image);
		}
		$i->clear();
		$i->destroy();*/
		return $temp.'/'.$recipe->id.'/'.$recipe->image;
	}

}