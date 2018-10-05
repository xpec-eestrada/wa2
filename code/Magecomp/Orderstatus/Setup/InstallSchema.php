<?php
namespace Magecomp\Orderstatus\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
   
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'orderstatus'
         */
		$table = $installer->getConnection()
            ->newTable($installer->getTable('orderstatus'))
			->addColumn(
                'orderstatus_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true],
                'Entity ID'
			)
			->addColumn(
                'order_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Status'
            )
			->addColumn(
                'order_parent_state',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Parent Status'
            )
			->addColumn(
                'order_is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Is Active'
            )
			->addColumn(
                'order_is_system',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Is System'
            )
			->addColumn(
                'order_notify_by_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Notify By Email'
            );
        $installer->getConnection()->createTable($table);
		
		/**
         * Create table 'orderstatustemplate'
         */
		 $table = $installer->getConnection()
            ->newTable($installer->getTable('orderstatustemplate'))
			->addColumn(
                'orderstatustemplate_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true],
                'Entity ID'
			)
			->addColumn(
                'order_status_id',
                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                 null,
                [],
                'Status Id'
            )
			->addColumn(
                'order_store_id',
                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                 null,
                [],
                'Store Id'
            )
			->addColumn(
                'order_template_id',
                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                 null,
                [],
                'Template Id'
            );
        $installer->getConnection()->createTable($table);
    }
}