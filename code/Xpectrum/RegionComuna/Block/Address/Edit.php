<?php
namespace Xpectrum\RegionComuna\Block\Address;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Customer\Block\Address\Edit{
    public function getComuna(){
        return '';
    }

}
