<?php

namespace Xpectrum\LocaleLatino\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsavebefore implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct();
        
        //Price
        $price = $_product->getPrice();
        if ((int)$price > 0) {
            $precio = (int)str_replace(',', '.', str_replace('.', '', $price));
            $_product->setPrice($precio);
        }
        
        //Special Price
        $specialPrice = $_product->getSpecialPrice();
        if ((int)$specialPrice > 0) {            
            $sprecio = (int)str_replace(',', '.', str_replace('.', '', $specialPrice));
            $_product->setSpecialPrice($sprecio);
        }
        
        //Cost
        $cost = $_product->getCost();
        if ((int)$cost > 0) {            
            $costo = (int)str_replace(',', '.', str_replace('.', '', $cost));
            $_product->setCost($costo);
        }
        
        //Merchant Price
        $merchantPrice = $_product->getMerchantPrice();
        if ((int)$merchantPrice > 0) {            
            $precioMercado = (int)str_replace(',', '.', str_replace('.', '', $merchantPrice));
            $_product->setMerchantPrice($precioMercado);
        }
    }
}