<?php
namespace Xpectrum\Wa2\Plugin;

class Toolbar
{
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
    }

    /**
    * Plugin
    *
    * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
    * @param \Closure $proceed
    * @param \Magento\Framework\Data\Collection $collection
    * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
    */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Closure $proceed,
        $collection
    )
    {
        $currentOrder = $toolbar->getCurrentOrder();
        $result = $proceed($collection);

        if ($currentOrder) {
            switch ($currentOrder) {
                case 'new':
                    if($toolbar->getCurrentDirection()=='desc'){
                        $collection->getSelect()
                            ->order('created_at DESC');
                    }else{
                        $collection->getSelect()
                            ->order('created_at ASC');
                    }
                break;
                default:
                    $collection->setOrder($toolbar->getCurrentOrder(), $toolbar->getCurrentDirection());
                break;
            }
        }
        
        return $this;
    }
}
