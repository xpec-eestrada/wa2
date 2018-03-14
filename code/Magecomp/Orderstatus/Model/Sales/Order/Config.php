<?php
namespace Magecomp\Orderstatus\Model\Sales\Order;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magecomp\Orderstatus\Model\OrderstatusFactory;


class Config extends \Magento\Sales\Model\Order\Config
{
	protected $scopeConfig;
	protected $_modelStatusFactory;
	private $statuses;
		
    public function getStatuses()
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		
		$hideState = false;
		$this->scopeConfig = $om->get('Magento\Framework\App\Config\ScopeConfigInterface');
		if ($this->scopeConfig->getValue('orderstatus_configuration/orderstatusoption/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
		{
			$hideState = true;
		}
			
		$statuses = parent::getStatuses();
		
		$this->_modelStatusFactory = $om->get('Magecomp\Orderstatus\Model\OrderstatusFactory');
		$statusesCollection = $this->_modelStatusFactory->create()->getCollection()->load();
		
		if ($statusesCollection->getSize() > 0 && $hideState)
		{
			foreach ($this->_getCollection() as $item)
			{
				if ($item->getState()) {
                	$states[$item->getState()] = __($item->getData('label'));
            	}
			}
			
			foreach ($states as $stateLabel => $state)
			{
				foreach ($statusesCollection as $status)
				{
					if ($status->getData('order_is_active') && !$status->getData('order_is_system'))
					{
						// checking if we should apply status to the current state
						$parentStates = [];
						if ($status->getOrderParentState())
						{
							$parentStates = explode(',', $status->getOrderParentState());
						}
						if (!$parentStates || in_array(strtolower($state), $parentStates))
						{
							$elementName = strtolower($state) . '_' . $status->getId();
							$statuses[$elementName] = ( $hideState ? '' : $stateLabel . ': ' ) . __($status->getStatus());
						}
					}
				}
			}
		}
		return $statuses;
	}
        
	public function getStateStatuses($stateToGetFor, $addLabels = true)
	{
		$hideState = false;
		
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$this->scopeConfig = $om->get('Magento\Framework\App\Config\ScopeConfigInterface');
		
		if ($this->scopeConfig->getValue('orderstatus_configuration/orderstatusoption/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
		{
			$hideState = true;
		}
		
		$statuses = parent::getStateStatuses($stateToGetFor, $addLabels);
		
		$this->_modelStatusFactory = $om->get('Magecomp\Orderstatus\Model\OrderstatusFactory');
		$statusesCollection = $this->_modelStatusFactory->create()->getCollection()->load();
		
		if ($statusesCollection->getSize() > 0 && $hideState)
		{
			foreach ($this->_getCollection() as $item)
			{
				if ($item->getState()) {
                	$states[$item->getState()] = __($item->getData('label'));
            	}
			}
			
			foreach ($states as $stateLabel => $state)
			{
				$isnewshow = 0;
				if($stateToGetFor == 'new' && $stateLabel == 'new' && $state == 'Pending')
				{
					$isnewshow = 1;
				}
				
				if ($stateToGetFor == strtolower($state) || $isnewshow == 1)
				{
					foreach ($statusesCollection as $status)
					{
						if ($status->getData('order_is_active') && !$status->getData('order_is_system'))
						{
							// checking if we should apply status to the current state
							$parentStates = [];
							if ($status->getOrderParentState())
							{
								$parentStates = explode(',', $status->getOrderParentState());
							}
							if (!$parentStates || in_array(strtolower($state), $parentStates))
							{
								$elementName = strtolower($state) . '_' . $status->getId();
								if ($addLabels)
								{
									$statuses[$elementName] = ( $hideState ? '' : $stateLabel . ': ' ) . __($status->getOrderStatus());
								} else 
								{
									$statuses[] = $elementName;
								}
							}
						}
					}
				}
			}
		}
		
		return $statuses;
	}
        
	public function getStatusLabel($code)
	{
		
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$this->scopeConfig = $om->get('Magento\Framework\App\Config\ScopeConfigInterface');
		
		$hideState = false;
		if ($this->scopeConfig->getValue('orderstatus_configuration/orderstatusoption/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
		{
			$hideState = true;
		}
		$statusLabel = parent::getStatusLabel($code);
		if($code && $hideState) //if($statusLabel != '')
		{
			$this->_modelStatusFactory = $om->create('Magecomp\Orderstatus\Model\OrderstatusFactory');
			$statusesCollection = $this->_modelStatusFactory->create()->getCollection();
			if ($statusesCollection->getSize() > 0)
			{
				foreach ($this->_getCollection() as $item)
				{
					if ($item->getState()) {
						$states[$item->getState()] = __($item->getData('label'));
					}
				}
				
				foreach ($states as $stateLabel => $state)
				{
					foreach ($statusesCollection as $status)
					{
						if ($status->getData('order_is_active') && !$status->getData('order_is_system'))
						{
							// checking if we should apply status to the current state
							$parentStates = [];
							if ($status->getOrderParentState())
							{
								$parentStates = explode(',', $status->getOrderParentState());
							}
							if (!$parentStates || in_array(strtolower($state), $parentStates))
							{
								$elementName = strtolower($state) . '_' . $status->getId();
								if ($code == $elementName)
								{
									$statusLabel = ( $hideState ? '' : $stateLabel . ': ' ) . __($status->getOrderStatus());
									break(2);
								}
							}
						}
					}
				}
			}
		}
		return $statusLabel;
	}
	
	protected function _getStatuses($visibility)
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
        if ($this->statuses == null) {
            foreach ($this->_getCollection() as $item) 
			{
                $visible = (bool) $item->getData('visible_on_front');
                $this->statuses[$visible][] = $item->getData('status');
            }
			
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
						 $visible = (bool) $status->getData('order_is_active');
						 $this->statuses[$visible][] = $curdata.'_'.$status->getId();
					}
				}
			}
		
        }
        return $this->statuses[(bool) $visibility];
    }
}