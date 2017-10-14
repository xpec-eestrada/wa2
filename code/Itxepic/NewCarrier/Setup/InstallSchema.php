<?php
namespace Itxepic\NewCarrier\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Itxepic\NewCarrier\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();		
			$quoteTable = $installer->getTable('quote');
			$columns = [
				'store_pickup' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,	
					'nullable' => false,
					'comment' => 'Store Pickup',
				]
			];

			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($quoteTable, $name, $definition);
			}

			$salesTable = $installer->getTable('sales_order');
			$salesColumns = [
				'store_pickup' => [
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'nullable' => false,
					'comment' => 'Store Pickup',
				]
			];
			foreach ($salesColumns as $name => $definition) {
				$connection->addColumn($salesTable, $name, $definition);
			}
			$installer->endSetup();
    }
}
