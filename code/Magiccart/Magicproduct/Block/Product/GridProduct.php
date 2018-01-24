<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-08-05 17:45:44
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class GridProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_objectManager;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;
    
    protected $_limit; // Limit Product

    protected $_catIds; // all categories of store.

    protected $_stockFilter;
    protected $_stockFilter2;
    protected $scopeConfig;


    /**
     * @param Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        CategoryRepositoryInterface $categoryRepository,

        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockFilter2,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,

        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->_stockFilter = $stockFilter;
        $this->_stockFilter2 = $stockFilter2;
        $this->urlHelper = $urlHelper;
        $this->_objectManager = $objectManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->categoryRepository = $categoryRepository;
        parent::__construct( $context, $data );
    }

    public function getTypeFilter()
    {
        $type = $this->getRequest()->getParam('type');
        if(!$type){
            $type = $this->getActivated(); // get form setData in Block
        }
        return $type;
    }

    public function getWidgetCfg($cfg=null)
    {
        $info = $this->getRequest()->getParam('info');
        if($info){
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;          
        }else {
            $info = $this->getCfg();
            if(isset($info[$cfg])) return $info[$cfg];
            return $info;
        }
    }

    public function getLoadedProductCollection()
    {

        // if(is_null($this->_catIds)){
        //     $category = $this->categoryRepository->get($this->_storeManager->getStore()->getRootCategoryId());
        //     $this->_catIds = explode(',',$category->getAllChildren());
        // }
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->_productCollectionFactory->create();
        }

        $this->_limit = $this->getWidgetCfg('limit');
        $type = $this->getTypeFilter();
        $fn = 'get' . ucfirst($type);
        $collection = $this->{$fn}($this->_productCollection);
        // $collection->addCategoriesFilter(['in' => $this->_catIds]);
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)->addStoreFilter();
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );
        $collection->getSelect()->where("stock_status_index.stock_status = 1");
        return $collection;
    }


    public function getBestseller($collection){

        $report = $this->_objectManager->get('\Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory')->create()->setModel('Magento\Catalog\Model\Product');
        $report->setPageSize($this->_limit)->setCurPage(1);
        $producIds = array();
        foreach ($report as $product) {
            $producIds[] = $product->getProductId();
        }

        $collection->addAttributeToFilter('entity_id', array('in' => $producIds));
        $collection->getSelect()->where("stock_status_index.stock_status = 1");
        
        return $collection;
        
    }

    public function getFeatured($collection)
    {
        $collection->addAttributeToFilter('featured', '1')->setPageSize($this->_limit)->setCurPage(1);
        $collection->getSelect()->where("stock_status_index.stock_status = 1");

        return $collection;

    }

    public function getLatest($collection){

        $collection->addAttributeToSort('entity_id', 'desc')->setPageSize($this->_limit)->setCurPage(1);
        $collection->getSelect()->where("stock_status_index.stock_status = 1");

        return $collection; 
    }

    public function getMostviewed($collection){
 
        $report = $this->_objectManager->get('\Magento\Reports\Model\ResourceModel\Report\Product\Viewed\CollectionFactory')->create()->setModel('Magento\Catalog\Model\Product');
        $report->setPageSize($this->_limit)->setCurPage(1);
        $producIds = array();
        foreach ($report as $product) {
            $producIds[] = $product->getProductId();
        }

        $collection->addAttributeToFilter('entity_id', array('in' => $producIds));
        $collection->getSelect()->where("stock_status_index.stock_status = 1");
        return $collection;

    }

    public function getNew($collection) {

        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort('news_from_date', 'desc')
        ->setPageSize($this->_limit)->setCurPage(1);
        $collection->getSelect()->where("stock_status_index.stock_status = 1");

        return $collection;
    }

    public function getRandom($collection) {

        $collection->getSelect()->order('rand()');
        // getNumProduct
        $collection->setPageSize($this->_limit)->setCurPage(1);
        return $collection;
    }

    public function getRecently($collection) {

        // \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory

    }

    public function getSale($collection){

        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort('special_to_date', 'desc')
        ->setPageSize($this->_limit)->setCurPage(1);
        $collection->getSelect()->where("stock_status_index.stock_status = 1");

        return $collection;

    }

    public function getSpecial($collection) {


        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addStoreFilter()->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort('special_to_date', 'desc')
        ->setPageSize($this->_limit)->setCurPage(1);
        $collection->getSelect()->where("stock_status_index.stock_status = 1");

        return $collection;

    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

}
