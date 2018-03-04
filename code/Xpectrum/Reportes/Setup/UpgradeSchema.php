<?php

namespace Xpectrum\Reportes\Setup;

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
    private static $connectionName = 'xpectrum_shippingmethods';
    
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addColumnReport($setup);
        }
        
        $setup->endSetup();
    }

    
    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    private function addColumnReport(SchemaSetupInterface $installer){
        $installer->getConnection()->addColumn(
            $installer->getTable('xpec_indx_shipping'),
            'id_comuna',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 11,
                'comment' => 'Id Comuna'
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable('xpec_indx_shipping'),
            'nombre_comuna',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Nombre Comuna'
            ]
        );
    }
}
