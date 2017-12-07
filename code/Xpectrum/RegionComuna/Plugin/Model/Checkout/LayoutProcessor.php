<?php
namespace Xpectrum\RegionComuna\Plugin\Model\Checkout;
class LayoutProcessor 
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
     
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $data=array();
        $data[]=array('value'=>'','label'=>'Seleccione Región');

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['xpec_comuna'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'xpec_comuna',
            ],
            'dataScope' => 'shippingAddress.xpec_comuna',
            'label' => 'Comuna',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 101,
            'id' => 'xpec_comuna',
            'options' => $data
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['xpec_prefijo_telefono2'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'id' => 'xpec_prefijo_telefono2',
            ],
            'dataScope' => 'shippingAddress.xpec_prefijo_telefono2',
            'label' => 'Indicativo',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 102,
            'value' => '+56',
            'readonly' => true,
            'disabled' => true,
            'id' => 'xpec_prefijo_telefono2'
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['xpec_prefijo_telefono2']['value'] = '+56';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['xpec_prefijo_telefono'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'id' => 'xpec_prefijo_telefono',
            ],
            'dataScope' => 'shippingAddress.xpec_prefijo_telefono',
            'label' => 'Indicativo',
            'provider' => 'checkoutProvider',
            'visible' => false,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 102,
            'value' => '+56',
            'id' => 'xpec_prefijo_telefono'
        ];
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['xpec_prefijo_telefono']['value'] = '+56';
        

        return $jsLayout;
    }
}