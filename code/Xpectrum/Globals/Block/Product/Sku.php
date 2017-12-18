<?php
namespace Xpectrum\Globals\Block\Product;

use Magento\Catalog\Model\Product;

class Sku extends \Magento\Framework\View\Element\Template{
    protected $_product = null;
    protected $_coreRegistry = null;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    public function getProduct(){
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }
    public function getSku(){
        $product = $this->getProduct();
        return $product->getSku();
    }

}
