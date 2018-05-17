<?php

namespace Xpectrum\FixSpecialPrice\Pricing\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product\LinkedProductSelectBuilderInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class LowestPriceOptionsProvider extends \Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProvider
{
    /**
     * @var ResourceConnection
     */
    private $resource;
    
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    
    /**
     * @var LinkedProductSelectBuilderInterface
     */
    private $linkedProductSelectBuilder;
    
    /**
     * @param ResourceConnection $resourceConnection
     * @param LinkedProductSelectBuilderInterface $linkedProductSelectBuilder
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        LinkedProductSelectBuilderInterface $linkedProductSelectBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->resource = $resourceConnection;
        $this->linkedProductSelectBuilder = $linkedProductSelectBuilder;
        $this->collectionFactory = $collectionFactory;
        
        parent::__construct(
            $resourceConnection,
            $linkedProductSelectBuilder,
            $collectionFactory
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getProducts(ProductInterface $product)
    {
        if (!isset($this->linkedProductMap[$product->getId()])) {
            $productIds = $this->resource->getConnection()->fetchCol(
                '(' . implode(') UNION (', $this->linkedProductSelectBuilder->build($product->getId())) . ')'
            );

            $this->linkedProductMap[$product->getId()] = $this->collectionFactory->create()
                ->addAttributeToSelect(
                    ['price', 'special_price', 'special_from_date', 'special_to_date', 'tax_class_id']
                )
                ->addIdFilter($productIds)
                ->getItems();
        }
        return $this->linkedProductMap[$product->getId()];
    }
}