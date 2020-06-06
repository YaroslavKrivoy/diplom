<?php
namespace Webfitters\Importer\Console\Commands;

class CategoryCreate extends \Symfony\Component\Console\Command\Command {

	protected $category;
	private $tree = array(
		//Cheese Boxes
		76 => array(),
		//Fresh Cut Cheese
		77 => array(
		 	'Blue Cheese',
		 	'Brick Cheese',
		 	'Cheddar - Flavored',
		 	'Cheddar - Mild to Aged',
		 	'Colby Cheese',
		 	'Gouda & Edam Cheese',
		 	'Monterey Jack Cheese',
		 	'Processed Cheese',
		 	'Swiss Cheese',
		 	'WI Made Italian Cheese'
		),
		//Curds & Spread
		78 => array(
		 	'Cheese Curds',
		 	'Cheese Curds Flavored',
		 	'Cheese Spreads',
		 	'Speciality Cheese'
		),
		//Footwear
		79 => array(
		 	'Womens Minnetonka',
		 	'Mens Minnetonka',
		 	'Kids Minnetonka',
		 	'Womens Old Friends',
		 	'Mens Old Friends',
		 	'Kids Old Friends'
		),
		//Mustards & More
		80 => array(
		 	'Mustards',
		 	'Jams',
		 	'BBQ Sauces',
		 	'Salsa & Spices'
		),
		//Smoked Meats
		81 => array(
		 	'Sausage',
		 	'Jerky',
		 	'Snack Sticks'
		),
		//Sweets n' treats
		82 => array(
		 	'Caramels',
		 	'Pecan Logs',
		 	'Popcorn',
		 	'Chocolate'
		),
		//wearables
		83 => array(
		 	'Lazy Ones PJ\'s',
		 	'T-Shirts & Sweatshirts',
		 	'Stormy Kromer Hats'
		),
		//special selections
		84 => array(
		 	'Kitchen Utensils',
		 	'Packer Backer'
		)
	);

	public function __construct(
		\Magento\Catalog\Model\CategoryFactory $category,
		$name = null, 
		array $data = []
	){
		$this->category=$category;
		parent::__construct($name);
	}

	protected function configure(){
		$this->setName('webfitters:categorycreate')->setDescription('Creates new category tree.');
	}

	protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output){
		foreach($this->tree as $parent => $children){
			foreach($children as $new){
				$category=$this->category->create();
				$category->setName($new);
				$category->setIsActive(true);
				$category->setParentId($parent);
				$category->setStoreId(0);
				$category->setDescription('');
				$category->setMetaKeywords('');
				$category->setMetaTitle('');
				$category->setMetaDescription('');
				$category->setPath('1/2'.'/'.$parent);
				$category->save();
			}
		}
	}

}