<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2017-09-01T16:28:38+00:00
 * File:          Block/Adminhtml/Element/ColorPicker.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Block\Adminhtml\Element;

class ColorPicker extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    public function getElementHtml()
    {
        $value = $this->getData('value');
        if ($value == '') {
            $value = '#000000';
        }
        $elementHtml = <<<EOT
<style type="text/css">
    #color_picker {
      height: 35px;
      width: 100px;
      margin: 0 !important;
      padding: 0 !important;
      border: 0;
    }
</style>
<input id="color_picker" name="color_picker" data-ui-id="sales-order-status-edit-container-form-fieldset-element-text-color" class="input-text admin__control-text" type="color" value="{$value}" >
EOT;
        return $elementHtml;
    }
}
