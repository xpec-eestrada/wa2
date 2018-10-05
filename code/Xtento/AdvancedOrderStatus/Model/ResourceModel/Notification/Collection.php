<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2015-11-26T12:57:04+00:00
 * File:          Model/ResourceModel/Notification/Collection.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Model\ResourceModel\Notification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource table
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            'Xtento\AdvancedOrderStatus\Model\Notification',
            'Xtento\AdvancedOrderStatus\Model\ResourceModel\Notification'
        );
    }
}
