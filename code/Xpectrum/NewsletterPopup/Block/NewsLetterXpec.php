<?php

namespace Xpectrum\NewsletterPopup\Block;

use Magento\Framework\View\Element\Template\Context;

class NewsLetterXpec extends \Magento\Framework\View\Element\Template
{
    protected $_logo;
    
    public function __construct(
        Context $context,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        array $data = [])
    {
        $this->_logo = $logo;
        parent::__construct($context, $data);
    }
    
    public function getCurrentStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
    
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    public function getLogoSrc()
    {
        return $this->_logo->getLogoSrc();
    }
}