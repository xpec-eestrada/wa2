<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2017-09-01T15:39:24+00:00
 * File:          Setup/UpgradeSchema.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the Paypal module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Added in version 2.1.1
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_status'),
            'color',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 7,
                'nullable' => true,
                'comment' => 'Order Status Color'
            ]
        );

        $setup->endSetup();
    }
}
