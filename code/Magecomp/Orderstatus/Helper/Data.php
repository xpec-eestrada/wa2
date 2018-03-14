<?php

namespace Magecomp\Orderstatus\Helper;

use Magecomp\Orderstatus\Model\OrderstatusFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    /**
     * @var StatusFactory
     */
    protected $_modelStatusFactory;

    public function __construct(Context $context, 
        ScopeConfigInterface $configScopeConfigInterface, 
        OrderstatusFactory $modelStatusFactory)
    {
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_modelStatusFactory = $modelStatusFactory;

        parent::__construct($context);
    }

    public function getSystemStatuses()
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Psr\Log\LoggerInterface');
		$storeManager->info('from Helper getSystemStatuses() called');
        $statusList = [];
        $statuses = $this->_configScopeConfigInterface->getValue('global/sales/order/statuses', 'default')->asArray();
        foreach ($statuses as $alias => $data)
        {
            if (!isset($data['@']['status']))
            {
                $statusList[$data['label']] = [
                    'custom_name'   => $this->_modelStatusFactory->create()->load($alias, 'alias')->getStatus(),
                    'alias'         => $alias,
                ];
            }
        }
        return $statusList;
    }
}