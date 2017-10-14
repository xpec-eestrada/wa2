<?php
namespace Xpectrum\Servipag\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ){
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('servipag')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('servipag')
            )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ],
                'ID'
            )
            ->addColumn(
                'id_servipag',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'ID servipag'
            )
            ->addColumn(
                'id_cliente',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => false,
                    'nullable' => true,
                    'primary'  => false,
                    'unsigned' => true
                ],
                'ID cliente'
            )
            ->addColumn(
                'id_pedido',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'ID pedido'
            )
            ->addColumn(
                'estado_pago',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'Estado del pago'
            )
            ->addColumn(
                'monto',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => false,
                    'nullable' => true,
                    'primary'  => false,
                    'unsigned' => true,
                ],
                'Monto total del pedido'
            )           
            ->addColumn(
                'fecha',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Fecha del pedido'
            )
            ->addIndex(
                $installer->getIdxName('servipag', ['id_servipag']),
                ['id_servipag']
            )
            ->setComment('Tabla de transacciones Servipag');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}