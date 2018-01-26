<?php
namespace Xpectrum\ProductosSinStock\Model\Indexer;

use Magento\Framework\Indexer\CacheContext;
class ProductOutStockIndexer implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    private $cacheContext;
    public $logger;
    protected $_productCollectionFactory;
    protected $productStatus;
    protected $productVisibility;
    protected $stockInventory;


    public function __construct(
        \Xpectrum\ProductosSinStock\Logger\Logger $logger,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockInventory
    ) {
        $this->_productCollectionFactory         = $productCollectionFactory;
        $this->logger                            = $logger;
        $this->productStatus                     = $productStatus;
        $this->productVisibility                 = $productVisibility;
        $this->stockInventory=$stockInventory;
    }
    public function execute($ids){
    }
    public function executeFull(){
        // $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        // $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        // $connection     = $resource->getConnection();
        try {
            $fecha = date("Y-m-d H:i:s");
            $this->logger->info($fecha.'  Iniciando Transacción...');

            $collection = $this->_productCollectionFactory->create();
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
            $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
                ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);

            
            //$this->logger->info('Sql: '.$collection->getselect()->__toString());

            $i=0;
            $j=0;
            foreach($collection as $product){
                $swvalidar=false;
                if($product->getSku() == 'J302N'){
                    $this->logger->info("Sku: ".$product->getSku());
                    $swvalidar=true;
                }
                $swoutstock=true;
                $i++;
                if(method_exists($product->getTypeInstance(),"getUsedProducts")){
                    $_children = $product->getTypeInstance()->getUsedProducts($product);
                    if(isset($_children) && is_array($_children) && count($_children)>0  ){
                        $nom=0;
                        foreach($_children as $child){
                            $nom++;
                            $stockitem = $this->stockInventory->getStockItem(
                                $child->getId(),
                                $child->getStore()->getWebsiteId()
                            );
                            if($stockitem->getQty()>0){
                                $swoutstock=false;
                                $stockitem->setIsInStock(true);
                                $stockitem->save();
                            }else{
                                $stockitem->setIsInStock(false);
                                $stockitem->save();
                            }
                            if($swvalidar){
                                $this->logger->info("Sku: ".$child->getSku().' -- Stock: '.$child->getQty().' Stock2: '.$stockitem->getQty());
                            }
                        }
                    }
                }
                $stockItem = $this->stockInventory->getStockItemBySku($product->getSku());
                if($swoutstock){
                    $stockItem->setIsInStock(false);
                    $stockItem->save();
                    $this->logger->info("Sku: ".$product->getSku().' fue establecido como out of stock');
                    $j++;
                }else{
                    $this->logger->info("Sku: ".$product->getSku().' fue establecido como out of stock');
                    $stockItem->setIsInStock(true);
                    $stockItem->save();
                }
            }
            $this->logger->info("Productos leidos: ".$i.' Out Stock: '.$j);
            $this->logger->info(date("Y-m-d H:i:s")." End Transaction...");
        } catch (\Exception $e) {
            $this->logger->info(date("Y-m-d H:i:s")." - Error : ".$e->getMessage());
        }
    }
    public function executeList(array $ids){
        
    }
    public function executeRow($id){
        
    }
    protected function getCacheContext(){
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }
}