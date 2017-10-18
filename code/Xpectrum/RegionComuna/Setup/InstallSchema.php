<?php
/**
* Copyright © 2017 Xpectrum. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Xpectrum\RegionComuna\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
    * {@inheritdoc}
    * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
          /**
          * Create table 'greeting_message'
          */
        $setup->startSetup();
        $table = $setup->getConnection()
            ->newTable($setup->getTable('xpec_comunas'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'auto_increment' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID Comuna'
            )
            ->addColumn(
                'nombre',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Nombre de Comuna'
            )
            ->addColumn(
                'idregion',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Id Region'
            )
            ->setComment("Tabla Comunas");
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}