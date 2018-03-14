<?php

namespace Xpectrum\NewsletterPopup\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->createTableXpecEmailCupon($setup);
        }
        
        $setup->endSetup();
    }

    private function createTableXpecEmailCupon($setup){
        $table = $setup->getConnection()
            ->newTable($setup->getTable('xpec_email_cupon_newsletter'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true],
                'Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Email'
            )
            ->addColumn(
                'cupon',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '50',
                ['nullable' => false, 'default' => ''],
                'Cupon asignado a email de newsletter'
            )
            ->addColumn(
                'estado',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '50',
                ['nullable' => false, 'default' => ''],
                'Estado del registro newsletter'
            )
            ->setComment("Xpec Tabla que relaciona emails de newsletter con cupon.");
            $setup->getConnection()->createTable($table);
    }
}
