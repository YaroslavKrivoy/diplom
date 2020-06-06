<?php
namespace Webfitters\Importer\Console\Commands;

class ProductCreate extends \Webfitters\Importer\Console\ImporterCommand {

	protected $dir;
	protected $product;
	protected $groupPrice;
	protected $productGallery;
	protected $groups = [];
	protected $wholemap = [
		"Ostrich" => [2, 45, 46], 
		"Miscellaneous" => [2, 45],
		"Meat Alternatives" => [2, 45],
		"Shipping" => [2, 45],
		"Gourmet Cheeses" => [2, 45],
		"Organic Cheese" => [2, 45],
		"Yogurt Cheese" => [2, 45],
		"Raw Milk Cheese" => [2, 45],
		"Amish Cheese" => [2, 45],
		"Supplements" => [2, 45],
		"Snacks" => [2, 45],
		"Compliments and Seasonings" => [2, 45],
		"Oils" => [2, 45],
		"Spices" => [2, 45],
		"Organic Fruits" => [2, 45],
		"Organic Vegetables" => [2, 45],
		"Holiday Combos" => [2, 45],
		"Holiday Combo" => [2, 45],
		"Kobe Beef" => [2, 45, 47],
		"Bison" => [2, 45, 48],
		"Kosher Bison" => [2, 45, 48],
		"buffalo tendons" => [2, 45, 48],
		"Organic Coffee" => [2, 45],
		"Pet Food" => [2, 45],
		"Kosher Meats" => [2, 45],
		"Jerky" => [2, 45],
		"Marinades" => [2, 45],
		"Organic and All Natural Beef" => [2, 45, 47],
		"Gluten Free Offerings" => [2, 45],
		"Organic and All Natural Free Range Chicken_cooked" => [2, 45, 49],
		"All Natural ABF Pork" => [2, 45, 50],
		"Organic Pork" => [2, 45, 50],
		"Pork" => [2, 45, 50],
		"Bison Hides" => [2, 45, 48],
		"Walnut Burgers" => [2, 45],
		"Organic and All Natural Free Range Chicken" => [2, 45, 49],
		"Poultry Meats" => [2, 45, 49],
		"Organic Produce" => [2, 45],
		"Organic Fruits and Vegetables" => [2, 45],
		"Other Healthy Products" => [2, 45],
		"Other Healthy Meats" => [2, 45],
		"Healthy Red Meats" => [2, 45],
		"Seafood" => [2, 45],
		"Fish/Seafood" => [2, 45],
		"Venison" => [2, 45, 54],
		"Elk" => [2, 45, 53],
		"Certified Organic Free Range Turkey" => [2, 45, 52],
		"Wild Boar" => [2, 45, 51],
		"Goat" => [2, 45],
		"Rabbit" => [2, 45],
		"Grass Fed and Finished Lamb" => [2, 45],
		"Duck/Game Birds" => [2, 45],
		"Quail" => [2, 45],
		"Guinea Hen" => [2, 45],
		"Cornish Hen" => [2, 45],
		"Pheasant" => [2, 45]
	];
	protected $catmap = [
		"Ostrich" => [2, 9, 10, 19], 
		"Venison" => [2, 9, 10, 23],
		"Bison" => [2, 9, 10, 12],
		"Kosher Bison" => [2, 9, 10, 12],
		"Elk" => [2, 9, 10, 15],
		"Supplements" => [2, 43, 9, 25, 38],
		"Snacks" => [2, 9, 25, 37, 42],
		"Goat" => [2, 9, 10, 17],
		"Rabbit" => [2, 9, 10, 21],
		"Gourmet Cheeses" => [2, 9, 25, 30],
		"Organic Cheese" => [2, 9, 25, 30, 39, 25, 26],
		"Yogurt Cheese" => [2, 9, 25, 30],
		"Raw Milk Cheese" => [2, 9, 25, 30],
		"Amish Cheese" => [2, 9, 25, 30],
		"Kobe Beef" => [2, 9, 10, 11],
		"All Natural ABF Pork" => [2, 9, 10, 20],
		"Organic Pork" => [2, 9, 10, 20, 39, 25, 26],
		"Pork" => [2, 9, 10, 20],
		"Organic and All Natural Beef" => [2, 9, 10, 11, 39, 25, 26],
		"Gluten Free Offerings" => [2, 9, 25, 29, 40],
		"Organic Coffee" => [2, 9, 25, 32, 39, 26],
		"Pet Food" => [2, 9, 25, 36, 41],
		"Bison Hides" => [2, 9, 25, 27],
		"Walnut Burgers" => [2, 9, 10, 24],
		"Organic Fruits" => [2, 9, 25, 33, 39, 26],
		"Organic Vegetables" => [2, 9, 25, 34, 39, 26],
		"Holiday Combos" => [2, 9, 25, 31],
		"Holiday Combo" => [2, 9, 25, 31],
		"Grass Fed and Finished Lamb" => [2, 9, 10, 18],
		"Organic and All Natural Free Range Chicken" => [2, 9, 10, 13, 39, 25, 26],
		"Certified Organic Free Range Turkey" => [2, 9, 10, 22, 39, 25, 26],
		"Duck/Game Birds" => [2, 9, 10, 14],
		"Organic and All Natural Free Range Chicken_cooked" => [2, 9, 10, 13, 39, 25, 26],
		"Organic Produce" => [2, 9, 25, 39, 26],
		"Organic Fruits and Vegetables" => [2, 9, 25, 39, 26],
		"Other Healthy Products" => [2, 9, 25],
		"Other Healthy Meats" => [2, 9, 10],
		"Healthy Red Meats" => [2, 9, 10],
		"Poultry Meats" => [2, 9, 10],
		"Seafood" => [2, 9, 10, 16, 44],
		"Fish/Seafood" => [2, 9, 10, 16, 44],
		"Kosher Meats" => [2, 9, 10],
		"Jerky" => [2, 9, 37, 42],
		"Marinades" => [2, 9, 25, 35],
		"Quail" => [2, 9, 10, 20],
		"Guinea Hen" => [2, 9, 10, 20],
		"Wild Boar" => [2, 9, 10, 20],
		"Cornish Hen" => [2, 9, 10, 14],
		"Pheasant" => [2, 9, 10, 20],
		"Compliments and Seasonings" => [2, 9, 25, 35],
		"Oils" => [2, 9, 25, 35],
		"Spices" => [2, 9, 25, 35],
		"buffalo tendons" => [2, 9, 25, 36, 41],
		"Miscellaneous" => [2, 9],
		"Meat Alternatives" => [2, 9, 10],
		"Shipping" => [2, 9]
	];

	public function __construct(
		\Magento\Framework\Filesystem\DirectoryList $dir,
		\Magento\Catalog\Model\ProductFactory $product, 
		\Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $groupPrice,
		\Magento\Catalog\Model\Product\Gallery\Processor $productGallery,
		\Magento\Customer\Model\ResourceModel\Group\Collection $group,
		\Magento\Framework\App\State $state,
		$name = null, 
		array $data = []
	){
		parent::__construct($state, $name, $data);
		$this->dir = $dir;
		$this->product = $product;
		$this->groupPrice = $groupPrice;
		$this->productGallery = $productGallery;
		$groups = $group->load();
		foreach($groups as $group){
			$this->groups[$group->getCustomerGroupCode()] = $group->getCustomerGroupId();
		}
	}

	protected function configure(){
		$this->setName('webfitters:product')->setDescription('Imports products from live database.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		$products = $this->db->query('
			SELECT `p`.*, `pc`.`name` AS `product_category` FROM `gs_product` AS `p` 
				LEFT JOIN `gs_product_category` AS `pc` ON `pc`.`id` = `p`.`product_category_id`
			WHERE `p`.`product_type` = "norm";
		')->fetchAll(\PDO::FETCH_OBJ);
		$progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($products));
		foreach($products as $product){
			try {
				$specials = $this->db->query('SELECT * FROM `gs_product_special` WHERE `product_id` = '.$product->id.' AND `end_date` > NOW();')->fetchAll(\PDO::FETCH_OBJ);
				$infos = $this->db->query('
					SELECT `pi`.*, `gc`.`name` AS `group_code` FROM `gs_product_info` AS `pi` 
						LEFT JOIN `gs_customer_category` AS `gc` ON `gc`.`id` = `pi`.`customer_category_id`
					WHERE `pi`.`product_id` = '.$product->id.';
				')->fetchAll(\PDO::FETCH_OBJ);
				$info = $this->db->query('SELECT * FROM `gs_product_info` WHERE `product_id` = '.$product->id.' AND `customer_category_id` = 1 LIMIT 0, 1;')->fetchAll(\PDO::FETCH_OBJ);
				$info = (count($info)>0)?$info[0]:null;
				$wholesale = (count($info)==0);
				$p = $this->product->create()->loadByAttribute('sku', $product->number);
				if(!$p){
					continue;
					$p = $this->product->create();
				}
				/*$p->setSku($product->number);
				$p->setName((($info)?$info->description_short:$product->description_short).' ('.$product->portion.')');
				$p->setDescription($product->description_long);
			    $p->setAttributeSetId(4);
			    $p->setStatus(1);
			    $p->setTypeId('simple');
			    $usable = (($wholesale)?$this->wholemap:$this->catmap);
			    $p->setCategoryIds(((isset($usable[$product->product_category]))?$usable[$product->product_category]:[]));
			    $p->setCost($product->cost);
			    $p->setPrice((($info)?$info->price:$product->cost));
				$p->setTierPrices(null);
				$p->setGroupPrice(null);
			    $p->setWeight($product->weight);
			    $p->setActive((($product->active=='yes')?true:false));
			    $p->setMetaTitle((($info)?$info->description_short:$product->description_short).' ('.$product->portion.')');
			    $p->setMetaDescription($product->description_long);
			    $p->setMetaKeyword($product->tags);
			    $p->setFeatured((($product->featured=='yes')?true:false));
			    $p->setVisibility(4);
			    $p->setWebsiteIds(array(0, 1));
			    $p->setStoreId(0);
			    $p->setStockData(array(
			        'use_config_manage_stock' => 1,
			        'manage_stock' => 0,
			        'is_in_stock' => 1*//*(($product->out_of_stock == 'no')?true:false)*//*,
			        'is_qty_decimal' => false,
			        'qty' => 10000*//*$product->inventory*/
			    /*));
			    $p->setServingSuggestions($product->serving_suggestions);
			    if($wholesale){
					$p->setWholesaleProduct(1);
			    }
			    if($nutrition = $this->getNutrition($product)){
			    	$url = str_replace($this->dir->getPath('pub').'/media/', '', $nutrition);
			    	$p->setNutritionInfo('<img class="nutrition-image" src="{{media url="'.$url.'"}}" />');
				}
				if($image = $this->getImage($product)){
			    	$p->addImageToMediaGallery($image, array('small_image', 'thumbnail', 'image'), false, false);
			    }
			    $p->save();*/
			    $p = $this->product->create()->load($p->getId());
			    $groupPricing = [];

			    /*$groupPricing = [];*/
			    $highest = 0;
			    if(is_array($infos)){
			    	foreach($infos as $i){
			    		if($i->price > $highest){
			    			$highest = $i->price;
			    		}
			    		$groupPricing[] = $this->groupPrice->create()->setCustomerGroupId($this->groups[$i->group_code])->setQty(1)->setValueLb(($i->price_lb > 0)?$i->price_lb:null)->setValue($i->price);
			    	}
			    }
			    $groupPricing[] = $this->groupPrice->create()->setCustomerGroupId(0)->setQty(1)->setValue($highest);
			    $p->setPrice($highest);
			    $p->setTierPrices($groupPricing);
			    $p->save();
			} catch(\Exception $e){var_dump($e->getMessage());}
			$progress->advance();
		}
		$progress->finish();
	}

	private function getNutrition($product){
		$temp = $this->dir->getPath('pub').'/media/catalog/product/nutrition';
		if(!file_exists($temp)){
			mkdir($temp);
		}
		if(!file_exists($temp.'/'.$product->id)){
			mkdir($temp.'/'.$product->id);
		}
		$info = $this->db->query('SELECT * FROM `gs_product_info` WHERE `product_id` = '.$product->id.' AND `customer_category_id` = 1 LIMIT 0, 1;')->fetchAll(\PDO::FETCH_OBJ);
		$info = ((count($info)>0)?$info[0]:null);
		if(!$info || $product->nutrition == ''){
			return null;
		}
		if(file_exists($temp.'/'.$product->id.'/'.$product->nutrition)){
			return $temp.'/'.$product->id.'/'.$product->nutrition;
		}
		$bucket = intval(intval($info->product_id)/256)+1;
		$file = @file_get_contents('https://blackwing.com/images/products/'.$bucket.'/'.$info->product_id.'/'.$info->id.'/'.rawurlencode($product->nutrition));
		if(!$file){
			return null;
		}
		$image = fopen($temp.'/'.$product->id.'/'.$product->nutrition, 'w+');
		fwrite($image, $file);
		fclose($image);
		return $temp.'/'.$product->id.'/'.$product->nutrition;
	}

	private function getImage($product){
		$temp = $this->dir->getPath('pub').'/media/catalog/product/imported';
		if(!file_exists($temp)){
			mkdir($temp);
		}
		if(!file_exists($temp.'/'.$product->id)){
			mkdir($temp.'/'.$product->id);
		}
		$info = $this->db->query('SELECT * FROM `gs_product_info` WHERE `product_id` = '.$product->id.' AND `customer_category_id` = 1 LIMIT 0, 1;')->fetchAll(\PDO::FETCH_OBJ);
		$info = ((count($info)>0)?$info[0]:null);
		if(!$info || $info->image == ''){
			return null;
		}
		if(file_exists($temp.'/'.$product->id.'/'.$info->image)){
			return null;
			return $temp.'/'.$product->id.'/'.$info->image;
		}
		$bucket = intval(intval($info->product_id)/256)+1;
		$file = @file_get_contents('https://blackwing.com/images/products/'.$bucket.'/'.$info->product_id.'/'.$info->id.'/'.rawurlencode($info->image));
		if(!$file){
			return null;
		}
		$image = fopen($temp.'/'.$product->id.'/'.$info->image, 'w+');
		fwrite($image, $file);
		fclose($image);
		$i = new \Imagick($temp.'/'.$product->id.'/'.$info->image);
		$dim = $i->getImageGeometry();
		if($dim['width'] < 550 && $dim['height'] < 550){
			$i->resizeImage(550, 550, \Imagick::FILTER_LANCZOS, 0.95, true);
			$i->writeImage($temp.'/'.$product->id.'/'.$info->image);
		}
		$i->clear();
		$i->destroy();
		return $temp.'/'.$product->id.'/'.$info->image;
	}

}