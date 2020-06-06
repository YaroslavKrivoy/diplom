<?php
namespace Webfitters\Custom\Console\Commands;

class CategoryCreate extends \Symfony\Component\Console\Command\Command {

	protected $category;
	private $tree = array(
		'Our Products' => array(
			'Meat' => array(
				'Beef',
				'Bison (Buffalo)',
				'Chicken',
				'Duck/Game Birds',
				'Elk',
				'Fish',
				'Goat',
				'Grass Fed Lamb',
				'Ostrich',
				'Pork',
				'Rabbit',
				'Turkey',
				'Venison',
				'Walnut Burgers'
			),
			'More' => array(
				'All Organic',
				'Bison Hides',
				'Gift Certificates',
				'Gluten Free',
				'Gourmet Cheeses',
				'Holiday Combos',
				'Organic Coffee',
				'Organic Fruits',
				'Organic Veggies',
				'Marinades',
				'Pet Food',
				'Snacks',
				'Suppliments'
			)
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
		$start = '1/2';
		$this->recurse($this->tree, $start, 2);
	}

	private function recurse($array, $start, $parentId){
		foreach($array as $key => $val){

			if(is_array($val)){
				$category=$this->category->create();
				$category->setName($key);
				$category->setIsActive(true);
				$category->setParentId($parentId);
				$category->setStoreId(0);
				$category->setDescription('');
				$category->setMetaKeywords('');
				$category->setMetaTitle('');
				$category->setMetaDescription('');
				$category->setPath($start);
				$category->save();
				$this->recurse($val, $start.'/'.$category->getId(), $category->getId());
			} else {
				$category=$this->category->create();
				$category->setName($val);
				$category->setIsActive(true);
				$category->setParentId($parentId);
				$category->setStoreId(0);
				$category->setDescription('');
				$category->setMetaKeywords('');
				$category->setMetaTitle('');
				$category->setMetaDescription('');
				$category->setPath($start);
				$category->save();
			}
		}
	}

}