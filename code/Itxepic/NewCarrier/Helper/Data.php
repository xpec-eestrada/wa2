<?php
namespace Itxepic\NewCarrier\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_ACTIVE = 'carriers/newcarrier/active';
    const XML_PATH_PICKUPSTORE = 'carriers/newcarrier/pickupstore';
    
    protected $_product;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Model\Product $product,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->_product = $product;
        $this->_moduleList= $moduleList;

        parent::__construct($context);
    }

    /**
     * Check if enabled
     *
     * @return string|null
     */
    public function isActive()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getPickUpStores($store = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_PICKUPSTORE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $value = $this->unserializeValue($value);
		
        $result = [];
        foreach ($value as $val) {
                $result[] = $val;
        }
        return $result;
    }
	
	protected function unserializeValue($value)
    {
      if (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return [];
        }
    }
}