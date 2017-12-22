<?php
namespace Xpectrum\AtributoAdicional\Plugin;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\DataObject;


class SaveOrder{
    protected $customerSession;

    protected $logger;

    protected $resultJsonFactory;
    /**
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * 
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository     
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Xpectrum\AtributoAdicional\Logger\Logger $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->customerSession=$customerSession;
        $this->resultJsonFactory=$resultJsonFactory;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if($this->customerSession->isLoggedIn()) {
            $mensaje='';
            $customerData = $this->customerSession->getCustomer();
            $email=$customerData->getData('email');
            try{
                $rut=$customerData->getData('rut');
                if(isset($rut) && !empty($rut)){
                    if(!$this->valida_rut($rut)){
                        $mensaje='Rut invalido.';
                        throw new \Exception($mensaje);
                    }
                }else{
                    $mensaje='Debe ingresar Rut.';
                    throw new \Exception($mensaje);
                }
            }catch(\Exception $err){
                $this->logger->info($mensaje.' Email: '.$email);
                throw new InputException(__($mensaje));
            }
        }
    }
    private function valida_rut($rut){
        if (!preg_match("/^[0-9.]+[-]{1}+[0-9kK]{1}/", $rut)) {
            return false;
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
        if ($dvr == strtoupper($dv))
            return true;
        else
            return false;
    }
}