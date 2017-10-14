<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Transbank\Webpay\Block\Checkout\Onepage\Failure;

class FailureDataTrans extends \Magento\Checkout\Block\Onepage\Failure{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context,$this->_checkoutSession, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return mixed
     */
    public function getRealOrderId()
    {
        return $this->_checkoutSession->getLastRealOrderId();
    }
    public function getRealOrderSession(){
        return $this->_checkoutSession;
    }

    /**
     *  Payment custom error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $error = $this->_checkoutSession->getErrorMessage();
        return $error;
    }

    /**
     * Continue shopping URL
     *
     * @return string
     */
    public function getContinueShoppingUrl()
    {
        return $this->getUrl('checkout/cart');
    }
    public function _toHtml(){
         if ($this->getTemplate() == 'Transbank_Webpay::checkout/onepage/failure.phtml'){
            $this->setTemplate('Transbank_Webpay::checkout/onepage/failure.phtml');
            $html = $this->fetchView($this->getTemplateFile());
            return $html;
        }
        return parent::_toHtml();
    }
}
