<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2017-10-11T08:38:28+00:00
 * File:          Helper/Module.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Helper;

class Module extends \Xtento\XtCore\Helper\AbstractModule
{
    protected $edition = '%!version!%';
    protected $module = 'Xtento_AdvancedOrderStatus';
    protected $extId = 'MTWOXtento_AdvancedOrderStatus988909';
    protected $configPath = 'advancedorderstatus/general/';

    // Module specific functionality below

    /**
     * @return array
     */
    public function getControllerNames()
    {
        return ['order', 'sales_order', 'adminhtml_sales_order', 'admin_sales_order'];
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return parent::isModuleEnabled();
    }
}
