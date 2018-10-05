<?php
namespace Magecomp\Orderstatus\Model;

class Orderstatus extends \Magento\Framework\Model\AbstractModel implements OrderstatusInterface, \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'orderstatus';
	
	//For Enable Options
	const STATUS_YES = 1;
    const STATUS_NO = 0;
	 
    protected function _construct()
    {
        $this->_init('Magecomp\Orderstatus\Model\ResourceModel\Orderstatus');
    }
 
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
	
	public function getAvailableoption()
    {
        return [self::STATUS_YES => __('Yes'), self::STATUS_NO => __('No')];
    }
	
	public function getAvailablestateoption()
	{
		return [
            ['value' => 'canceled', 'label' => __('Canceled')],
			['value' => 'closed', 'label' => __('Closed')],
			['value' => 'complete', 'label' => __('Complete')],
			['value' => 'fraud', 'label' => __('Suspected Fraud')],
			['value' => 'holded', 'label' => __('On Hold')],
			['value' => 'payment_review', 'label' => __('Payment Review')],
			['value' => 'paypal_canceled_reversal', 'label' => __('PayPal Canceled Reversal')],
			['value' => 'paypal_reversed', 'label' => __('PayPal Reversed')],
			['value' => 'pending', 'label' => __('Pending')],
			['value' => 'pending_payment', 'label' => __('Pending Payment')],
			['value' => 'pending_paypal', 'label' => __('Pending PayPal')],
			['value' => 'processing', 'label' => __('Processing')]
			
        ];	
	}
	
	
}