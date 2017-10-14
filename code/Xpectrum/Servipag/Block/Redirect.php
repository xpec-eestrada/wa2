<?php
namespace Xpectrum\Servipag\Block;

class Redirect extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Xpectrum\Servipag\Model\Servipag
     */
    protected $servipagPayment;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Xpectrum\Servipag\Model\ServipagPayment $servipagPayment,        
        array $data = []
    ){        
        parent::__construct($context, $data);
        
        $this->servipagPayment = $servipagPayment;
    }
    
    public function getXml1()
    {
        $result['xml'] = $this->servipagPayment->setXml1();
        $result['error'] = !empty($result['xml']) ? false : true;
        $result['url'] = \Xpectrum\Servipag\Model\ServipagPayment::SERVIPAG_URL;
        
        return $result;
    }
}