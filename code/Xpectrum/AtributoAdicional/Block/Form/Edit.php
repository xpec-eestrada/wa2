<?php
/**
 * Copyright © 2017 Xpectrum. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Xpectrum\AtributoAdicional\Block\Form;

use Magento\Customer\Model\Session as CustomerSession;

class Edit extends \Magento\Customer\Block\Form\Edit{

    protected $_customer;
    protected $_rut;
    protected $_numero_contacto;

    public function loadXpecCustomer(){
        if(!isset($this->_customer) || $this->_customer==null){
            $this->_customer=$this->getCustomer();
        }
        return $this->_customer;
    }
    public function getRutCustomer(){
        if(!isset($this->_rut) || $this->_rut==null){
            $customer   = $this->loadXpecCustomer();
            $this->_rut = ($customer->getCustomAttribute('rut')!==null)?$customer->getCustomAttribute('rut')->getValue():'';
        }
        return $this->_rut;
    }
    public function getNumeroContactoCustomer(){
        if(!isset($this->_numero_contacto) || $this->_numero_contacto==null){
            $customer   = $this->loadXpecCustomer();
            $this->_numero_contacto = ($customer->getCustomAttribute('numero_contacto')!==null)?$customer->getCustomAttribute('numero_contacto')->getValue():'';
        }
        return $this->_numero_contacto;
    }

}
