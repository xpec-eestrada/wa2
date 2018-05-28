<?php
namespace Magecomp\Orderstatus\Model\Sales\Order\Status;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Status\History as StatusHistory;
use Magento\Store\Model\StoreManagerInterface;

class History extends StatusHistory
{
	protected $_frameworkRegistry;
	
    public function __construct(Context $context, 
        Registry $registry, 
        ExtensionAttributesFactory $extensionFactory, 
        AttributeValueFactory $customAttributeFactory, 
        StoreManagerInterface $storeManager, 
        AbstractResource $resource = null, 
        AbstractDb $resourceCollection = null, 
        array $data = [])
    {
		$this->_frameworkRegistry = $registry;
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $storeManager, $resource, $resourceCollection, $data);
    }

    public function setIsCustomerNotified($flag = null)
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		try
		{	
			$statusModel_id = $this->_frameworkRegistry->registry('magecomp_history_status');
			$statusModel = $om->get('Magecomp\Orderstatus\Model\Orderstatus')->load($statusModel_id);
			if ($statusModel_id != '' && $statusModel_id > 0 && $statusModel->getOrderNotifyByEmail())
			{
				$flag = 1;
			}
			return parent::setIsCustomerNotified($flag);
		}
		catch (\Exception $e) 
		{
			//$this->logger->critical($e);
			$storeManager->info($e);
		}
    }
}