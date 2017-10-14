<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-11 23:15:05
 * @@Modify Date: 2016-03-24 18:10:08
 * @@Function:
 */

namespace Magiccart\Testimonial\Model\System\Config;

class Row implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            '1'=>   __('1 item per row'),
            '2'=>   __('2 item per row'),
            '3'=>   __('3 item per row'),
            '4'=>   __('4 item per row'),
            '5'=>   __('5 item per row'),
        ];
    }
}
