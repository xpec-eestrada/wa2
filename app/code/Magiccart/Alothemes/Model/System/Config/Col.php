<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-02-10 22:10:30
 * @@Modify Date: 2016-03-14 11:13:37
 * @@Function:
 */
namespace Magiccart\Alothemes\Model\System\Config;

class Col implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1,   'label'=>__('1 item per column')),
            array('value' => 2,   'label'=>__('2 items per column')),
            array('value' => 3,   'label'=>__('3 items per column')),
            array('value' => 4,   'label'=>__('4 items per column')),
            array('value' => 5,   'label'=>__('5 items per column')),
            array('value' => 6,   'label'=>__('6 items per column'))
        );
    }
}
