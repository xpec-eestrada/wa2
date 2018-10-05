<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2016-02-11T13:52:49+00:00
 * File:          Model/ResourceModel/Notification.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Model\ResourceModel;

class Notification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('sales_order_status_notification', 'notification_id');
    }

    /**
     * @param $statusCode
     */
    public function removeNotifications($statusCode)
    {
        $adapter = $this->getConnection();
        $adapter->delete($this->getMainTable(), ['status_code = ?' => $statusCode]);
    }
}
