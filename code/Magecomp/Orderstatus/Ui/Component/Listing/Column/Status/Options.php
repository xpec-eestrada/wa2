<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Orderstatus\Ui\Component\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Class Options
 */
class Options extends \Magento\Sales\Ui\Component\Listing\Column\Status\Options
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->collectionFactory->create()->toOptionArray();
        }
		
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$orderstatusobj = $om->get('Magecomp\Orderstatus\Model\OrderstatusFactory');
		$statusesCollection = $orderstatusobj->create()->getCollection()->load();
		$statusesCollection->addFieldToFilter('order_is_system',0);
		foreach ($statusesCollection as $status)
        {
			$this->options[] = ['value' => 'orderstatus_'.$status->getId(), 'label' => $status->getOrderStatus()];
		}
        return $this->options;
    }
}
