<?php
namespace Webfitters\Hero\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface {

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context) {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $connection->addColumn($setup->getTable('cms_page'), 'hero_parallax', array(
            'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'nullable'  => true,
            'comment'   => 'Whether to parallax hero image',
            'default' => '0'
        ));
        $connection->addColumn($setup->getTable('cms_page'), 'hero_image', array(
            'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable'  => true,
            'length'    => 255,
            'comment'   => 'Image to use for the hero',
            'default' => ''
        ));
        $connection->addColumn($setup->getTable('cms_page'), 'hero_text', array(
            'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable'  => true,
            'length'    => 255,
            'comment'   => 'Text to appear on hero image',
            'default' => ''
        ));
        $setup->endSetup();
        return;
    }

}