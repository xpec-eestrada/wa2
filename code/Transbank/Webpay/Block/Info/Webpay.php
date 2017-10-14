<?php

namespace Transbank\Webpay\Block\Info;

class Webpay extends \Magento\Payment\Block\Info
{
    /* Presenta información en bloque Payment Method */
    protected function _prepareSpecificInformation($transport = null)
    {        
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        $transport = parent::_prepareSpecificInformation($transport);
        $data = array();

        return $transport->setData(array_merge($data, $transport->getData()));    
    }    
}