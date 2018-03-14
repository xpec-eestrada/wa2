<?php

namespace Xpectrum\RegionComuna\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var string
     */
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createTableOrderAddress($setup);
        }
        
        $setup->endSetup();
    }
    private function createTableOrderAddress($setup){
        $table = $setup->getConnection()
            ->newTable($setup->getTable('xpec_order_address_data'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true],
                'Id'
            )
            ->addColumn(
                'id_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Id Orden'
            )
            ->addColumn(
                'id_region',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Id region'
            )
            ->addColumn(
                'name_region',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Nombre Region'
            )
            ->addColumn(
                'id_comuna',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Id de la comuna'
            )->addColumn(
                'name_comuna',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Nombre Comuna'
            )
            ->addColumn(
                'type_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Si direccion es de envio o facturacion'
            )
            ->setComment("Xpec Relaciona Dirección con la orden.");
        $setup->getConnection()->createTable($table);
    }
}
