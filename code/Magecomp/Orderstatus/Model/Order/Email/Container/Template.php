<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Orderstatus\Model\Order\Email\Container;

use Magento\Framework\Registry;

class Template extends \Magento\Sales\Model\Order\Email\Container\Template
{
    /**
     * @var int
     */
    protected $id;
	protected $scopeConfig;
	protected $_frameworkRegistry;
	protected $emailcollection;
    /**
     * Set email template id
     *
     * @param int $id
     * @return void
     */
    public function setTemplateId($id)
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$customState = false;
		$this->scopeConfig = $om->get('Magento\Framework\App\Config\ScopeConfigInterface');
		if ($this->scopeConfig->getValue('orderstatus_configuration/orderstatusoption/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
		{
			$customState = true;
		}
		
		//Check this is Order Status Change Email Or Not
		$statusmail = false;
		if($id == 'sales_email_order_comment_template' || $id == 'sales_email_order_comment_guest_template')
		{
			$statusmail = true;
		}
		
		if($customState && $statusmail) // Custom Code
		{
			$this->_frameworkRegistry = $om->get('Magento\Framework\Registry');
			$statusModel_id = $this->_frameworkRegistry->registry('magecomp_history_status');
			$order = $this->_frameworkRegistry->registry('current_order');
			// Check Current Status is custom and get Order Store Id
			
			if($statusModel_id != '' && $order->getStoreId() != '')
			{
				$statusinfo = $om->create('Magecomp\Orderstatus\Model\Orderstatus')->load($statusModel_id);
				
				if($statusinfo->getOrderNotifyByEmail()) // Custom Email Send Or Not
				{
					$this->emailcollection = $om->create('Magecomp\Orderstatus\Model\OrderstatusemailFactory');
					$etemplateCollection = $this->emailcollection->create()->getCollection();
					$etemplateCollection->addFieldToFilter('order_status_id',$statusModel_id);
					$etemplateCollection->addFieldToFilter('order_store_id',$order->getStoreId());
					$templateid = 0;
					
					foreach ($etemplateCollection as $emailtemplate)
					{
						$templateid = $emailtemplate->getOrderTemplateId();
					}
					$templatecode = '';
					if($templateid == 0) // Our Default Template
					{
						$templatecode = 'magecomp_orderstatus_change';
					}
					else
					{
						$emailinfo = $om->create('Magento\Email\Model\Template')->load($templateid);
						$templatecode = $emailinfo->getOrigTemplateCode();
					}
					$this->id = $templateid;
					//$this->id = $templatecode;
				}
				else
				{
					$this->id = $id;
				}
			}
			else
			{
				$this->id = $id;
			}
		}
		else // Default Code
		{
        	$this->id = $id;
		}
    }
}
