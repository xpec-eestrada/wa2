<?php
namespace Magecomp\Orderstatus\Ui\Component;
 
use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;
use Magecomp\Orderstatus\Model\ResourceModel\Orderstatus\CollectionFactory;

class Massaction implements JsonSerializable
{
    protected $options;
    protected $collectionFactory;
    protected $data;
    protected $urlBuilder;
    protected $urlPath;
    protected $paramName;
    protected $additionalData = [];

    public function __construct(
        CollectionFactory $collectionFactory,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }
 
    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $i=0;
        if ($this->options === null) {
            // get the massaction data from the database table
            $badgeColl = $this->collectionFactory->create()->addFieldToFilter('order_is_active',['eq'=>1]);
             
            if(!count($badgeColl)){
                return $this->options;
            }
            //make a array of massaction
            foreach ($badgeColl as $key => $badge) {
                $options[$i]['value']=$badge->getOrderstatusId();
                $options[$i]['label']=$badge->getOrderStatus();
                $i++;
            }
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'orderstatus_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];
 
                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }
 
                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }
			
            // return the massaction data
		   $this->options = array_values($this->options);
        }
        return $this->options;
    }
 
    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
          
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}