<?php

namespace MagicToolbox\MagicZoom\Model;

use Magento\Framework\Model\AbstractModel;

class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('MagicToolbox\MagicZoom\Model\ResourceModel\Config');
    }
}
