<?php

namespace Magecomp\Orderstatus\Model\Sales;

use Magecomp\Orderstatus\Model\ResourceModel\Orderstatus\Collection;
use Magecomp\Orderstatus\Model\OrderstatusemailFactory as StatusTemplateFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Eav\Model\Config as ModelConfig;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Helper\Data as HelperData;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory as PaymentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory as TrackCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Order extends ModelOrder
{
    protected $_statusCollection;
    protected $_helperData;
    protected $_configScopeConfigInterface;
    protected $_viewDesignInterface;
    protected $_modelTemplateFactory;
    protected $_statusTemplateFactory;

    public function addStatusHistoryComment($comment, $status = false)
    {
        $history = parent::addStatusHistoryComment($comment, $status);
        
        // checking is the new status is one of ours
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$this->_statusCollection = $om->create('Magecomp\Orderstatus\Model\OrderstatusFactory');
        $statusCollection = $this->_statusCollection->create()->getCollection();
        $statusCollection->addFieldToFilter('order_is_system',0);
		
        foreach ($statusCollection as $statusModel)
        {
            if ($statusModel->getId() == substr($status, strpos($status, '_') + 1))
            {
                // this is it!
				$this->_frameworkRegistry = $om->get('Magento\Framework\Registry');
				$this->_frameworkRegistry->unregister('magecomp_history_status');
                $this->_frameworkRegistry->register('magecomp_history_status',$statusModel->getId()); //$statusModel); //, true	
            }
        }
        return $history;
    }
}