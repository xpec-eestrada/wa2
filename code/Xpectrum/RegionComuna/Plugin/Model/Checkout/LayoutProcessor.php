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

        return $jsLayout;
    }
}