<?php
namespace Emipro\CustomNewsletterSender\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface{
     public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {   
         $installer = $setup;

        $installer->startSetup();

         $installer->run("ALTER TABLE  `".$setup->getTable('newsletter_queue')."` ADD  `customer_group` int");
//          $installer->getConnection()->createTable($table);
       
        $installer->endSetup();
        
     } 
    
}

