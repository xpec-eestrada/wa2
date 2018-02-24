<?php
/**
* Copyright © 2018 Xpectrum. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Xpectrum\Reportes\Setup;
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
            ->newTable($setup->getTable('xpec_indx_orders'))
            ->addColumn(
                'id_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true],
                'Id Order'
            )
            ->addColumn(
                'increment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '32',
                ['nullable' => false, 'default' => ''],
                'Id Incremental'
            )
            ->addColumn(
                'skus',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Skus'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '50',
                ['nullable' => false, 'default' => ''],
                'Cantidades'
            )
            ->addColumn(
                'productnames',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false, 'default' => ''],
                'Cantidades'
            )
            ->addColumn(
                'phone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '50',
                ['nullable' => false, 'default' => ''],
                'Teléfono'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false, 
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ],
                'Fecha de Creación'
            )
            ->addColumn(
                'total',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Total Pedido'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '30',
                ['nullable' => false, 'default' => ''],
                'Estado'
            )
            ->addColumn(
                'shipping_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Dirección de Envío'
            )
            ->addColumn(
                'billing_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Dirección de Facturación'
            )
            ->addColumn(
                'shipping_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false, 'default' => ''],
                'Descripción metodo de Envío'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '128',
                ['nullable' => false, 'default' => ''],
                'Email de Cliente'
            )
            ->addColumn(
                'shipping_price',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Precio de Envío'
            )
            ->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '128',
                ['nullable' => false, 'default' => ''],
                'Nombre de Cliente'
            )
            ->addColumn(
                'payment_method',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '128',
                ['nullable' => false, 'default' => ''],
                'Metodo de Pago'
            )
            ->setComment("Xpec Index Orders");
        $setup->getConnection()->createTable($table);
        /* Reporte de Envío */
        $table = $setup->getConnection()
        ->newTable($setup->getTable('xpec_indx_shipping'))
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
            'Id Order'
        )
        ->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '32',
            ['nullable' => false, 'default' => ''],
            'Id Incremental'
        )
        ->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'Sku'
        )
        ->addColumn(
            'payment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '80',
            ['nullable' => false, 'default' => ''],
            'Medio de Pago'
        )
        ->addColumn(
            'authocode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '80',
            ['nullable' => false, 'default' => ''],
            'Codigo de Autorización'
        )
        ->addColumn(
            'productname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'Nombre de Producto'
        )
        ->addColumn(
            'size',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '20',
            ['nullable' => false, 'default' => ''],
            'Talla'
        )
        ->addColumn(
            'color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '20',
            ['nullable' => false, 'default' => ''],
            'Color'
        )
        ->addColumn(
            'qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Cantidad'
        )
        ->addColumn(
            'shipping_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '20',
            ['nullable' => false, 'default' => ''],
            'Nombre de Producto'
        )
        ->addColumn(
            'price_product_base',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,],
            'Precio de producto sin iva'
        )
        ->addColumn(
            'price_product_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,],
            'Precio de producto con iva'
        )
        ->addColumn(
            'discount_percent',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,],
            'Porcentaje de Descuento'
        )
        ->addColumn(
            'price_order_base',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,],
            'Total de Orden sin iva'
        )
        ->addColumn(
            'price_order_base',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,],
            'Total de Orden sin iva'
        )
        ->addColumn(
            'price_order_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,],
            'Total de Orden con iva'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false, 
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
            ],
            'Fecha de Creación'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '30',
            ['nullable' => false, 'default' => ''],
            'Estado'
        )
        ->setComment("Xpec Index Shipping");
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}