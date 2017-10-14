<?php

namespace Transbank\Webpay\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TipoTrans implements ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            [
                'value' => 'TR_NORMAL_WS',
                'label' => __('TR_NORMAL_WS'),
            ],
            [
                'value' => 'TR_MALL_WS',
                'label' => __('TR_MALL_WS')
            ]
        ];
    }
}