<?php

namespace MagicToolbox\MagicZoom\Model\ResourceModel\Config;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MagicToolbox\MagicZoom\Model\Config', 'MagicToolbox\MagicZoom\Model\ResourceModel\Config');
    }
}
