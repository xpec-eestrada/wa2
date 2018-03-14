<?php
namespace Magecomp\Orderstatus\Controller\Adminhtml\Orderstatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Massstatus extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
	}

    protected function massAction(AbstractCollection $collection)
    {
		$id = $this->getRequest()->getParam('orderstatus_id');
		
		$finalState = 'orderstatus_'.$id;	
		
		$countCancelOrder = 0;
		
		foreach ($collection->getItems() as $order) 
		{
			$order->setStatus($finalState);
			$order->save();
            $countCancelOrder++;
        }
	
        if ($countCancelOrder) {
            $this->messageManager->addSuccess(__(' %1 order(s) Status Change..', $countCancelOrder));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/index/');
        return $resultRedirect;
    }
}