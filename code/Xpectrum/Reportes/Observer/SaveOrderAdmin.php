<?php
namespace Xpectrum\Reportes\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Type;

class SaveOrderAdmin implements ObserverInterface
{
    
    /**
    * @var \Magento\Framework\HTTP\ZendClientFactory
    */
    protected $_httpClientFactory;

    public $scopeConfig;

    protected $eav;

    private $_customer;

    private $countryFactory;

    private $_logger;

    /**
    * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Customer $customer,
        \Xpectrum\Reportes\Logger\LoggerAdmin $logger,
        \Magento\Directory\Model\CountryFactory $countryFactory){
        $this->_logger=$logger;
        $this->scopeConfig = $scopeConfig;
        $this->eav = $eavConfig;
        $this->_httpClientFactory = $httpClientFactory;
        $this->_customer = $customer;
        $this->countryFactory = $countryFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer){
        $objectManager          = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource               = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection             = $resource->getConnection();

        $order                  = $observer->getEvent()->getOrder();
        $orderId                = $order->getId();
        $incrementalId          = $order->getIncrementId();
        $shippingAddress        = $order->getShippingAddress();
        $tpayment               = $resource->getTableName('sales_order_payment');
        $shippingAddressLines   = $shippingAddress->getStreet();

        $payment                = $order->getPayment();
        $method                 = $payment->getMethodInstance();
        $methodTitle            = $method->getTitle();
        
        $logorder['Orden'] = array(
            'IdOrder' => $orderId,
            'IncrementId' => $incrementalId,
            'DateSaveLog' => date('d-m-Y H:i:s'),
            'PaymentMethod' => $methodTitle
        );

        $this->_logger->info(json_encode($logorder));
        
    }

}
