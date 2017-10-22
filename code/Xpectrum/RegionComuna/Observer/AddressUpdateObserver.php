<?php
namespace Xpectrum\RegionComuna\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddressUpdateObserver implements ObserverInterface {
	
    protected $date;
    public $logger;
    protected $_request;
	public function __construct(
            \Magento\Framework\Stdlib\DateTime\DateTime $date,
            \Magento\Framework\App\RequestInterface $request,
            \Xpectrum\RegionComuna\Logger\Logger $logger
	) {
        $this->logger = $logger;
        $this->date = $date;
        $this->_request = $request;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
        $address = $observer->getCustomerAddress();  
        $var = $this->_request->getPost();
        $this->logger->info("init transaccion...".print_r($var)  );
		if(!$address->hasDataChanges()){
            $this->logger->info("hay cambios");
			return $this;
		}
		// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $customerData=$objectManager->create('Magento\Customer\Model\Customer')->load($address->getCustomerId());
		// $customerData->setUpdatedAt($this->date->gmtDate());
		// $customerData->save();
	}
}