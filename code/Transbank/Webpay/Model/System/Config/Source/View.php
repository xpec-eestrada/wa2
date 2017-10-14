<?php

namespace Transbank\Webpay\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class View implements ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            [
                'value' => 'INTEGRACION',
                'label' => __('Integración'),
            ],
            [
                'value' => 'CERTIFICACION',
                'label' => __('Certificación')
            ],
            [
                'value' => 'PRODUCCION',
                'label' => __('Producción')
            ]
        ];
    }
}