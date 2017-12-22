<?php

namespace Xpectrum\AtributoAdicional\Model\Attribute\Backend;

class Rut extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * @var int $minimumValueLength
     */
    protected $minimumValueLength = 0;

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function afterLoad($object)
    {
        // your after load logic

        return parent::afterLoad($object);
    }

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $this->valida_rut($object);
        return parent::beforeSave($object);
    }
    /**
     * Vaalida que el rut sea valido
     *
     * @param \Magento\Framework\DataObject $object
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function valida_rut($object){
        $attributeCode  = $this->getAttribute()->getAttributeCode();
        $valor          = $object->getData($attributeCode);
        $rut            = trim($valor);

        if($rut == ""){
            throw new \Magento\Framework\Exception\LocalizedException(
                __('El Rut no puede estar vacio')
            );
        }

        if (!preg_match("/^[0-9.]+[-]{1}+[0-9kK]{1}/", $valor)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('El Rut "%1" es invalido', $rut)
            );
        }
        $rut = preg_replace('/[\.\-]/i', '', $rut);
        $dv = substr($rut, -1);
        $numero = substr($rut, 0, strlen($rut) - 1);
        $i = 2;
        $suma = 0;
        foreach (array_reverse(str_split($numero)) as $v) {
            if ($i == 8)
                $i = 2;
            $suma += $v * $i;
            ++$i;
        }
        $dvr = 11 - ($suma % 11);
        if ($dvr == 11)
            $dvr = 0;
        if ($dvr == 10)
            $dvr = 'K';
        if ($dvr != strtoupper($dv)){
            throw new \Magento\Framework\Exception\LocalizedException(
                __('El Rut "%1" es invalido', $valor)
            );
        }
    }
    /**
     * Get minimum attribute value length
     * 
     * @return int
     */
    public function getMinimumValueLength()
    {
        return $this->minimumValueLength;
    }
}