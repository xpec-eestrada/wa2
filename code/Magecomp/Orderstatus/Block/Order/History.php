<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Orderstatus\Block\Order;

/**
 * Sales order history block
 */
class History extends \Magento\Sales\Block\Order\History
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::order/history.phtml';
    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Psr\Log\LoggerInterface');
		
		$collection = $this->_orderConfig->getVisibleOnFrontStatuses();
		
		$orderstatusobj = $om->get('Magecomp\Orderstatus\Model\OrderstatusFactory');
		$statusesCollection = $orderstatusobj->create()->getCollection()->load();
		$statusesCollection->addFieldToFilter('order_is_system',0);
		
		foreach ($statusesCollection as $status)
        {
			$parentStates = [];
			if($status->getOrderParentState())
			{
				$parentStates = explode(',', $status->getOrderParentState());
				
				foreach($parentStates as $curdata)
				{
					$collection[] = $curdata.'_'.$status->getId();
				}
			}
		}
		
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->_orderCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->addFieldToFilter(
                'status',
                ['in' => $collection] //$this->_orderConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
    }
}
