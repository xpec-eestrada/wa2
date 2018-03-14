<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// namespace Magento\Catalog\Block\Product;
namespace Xpectrum\Globals\Block\Product;


use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Xpectrum\Categorias\Model\Marca;
use Magento\Framework\Api\Filter;

class ListProduct extends  \Magento\Catalog\Block\Product\ListProduct
{
    protected $_productCollection;
    protected $categoryRepository;
    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }

            // echo '<pre>'.$this->_productCollection->getSelect()->__toString().'</pre>';
            // die();

            // $joinConditions[] = 'e.row_id = pr_opt_val.row_id';
            // $joinConditions[] = 'attribute_id='.self::idattribute;

            // //$joinConditions[] = 'pr_opt_val.store_id=cat_index.store_id';
            // $joinConditions[] = 'pr_opt_val.store_id=0';
            // $joinConditions = implode(' AND ',$joinConditions);

            // $joinConditions2 = 'pr_opt_val.value=pr_opt_val_ord.option_id';

            // $joinIndex[]='xpec_index_price.row_id=e.row_id';
            // $joinIndex[]='(xpec_index_price.website_id=price_index.website_id)';
            // $joinIndex[]='xpec_index_price.customer_group_id=price_index.customer_group_id';
            // $joinIndex = implode(' AND ',$joinIndex);
            
            // $this->_productCollection->getSelect()->joinLeft(
            //     ['pr_opt_val' =>  $this->_productCollection->getTable('catalog_product_entity_int') ],
            //     $joinConditions,
            //     []
            // )->joinLeft(
            //     ['pr_opt_val_ord' =>  $this->_productCollection->getTable('eav_attribute_option') ],
            //     $joinConditions2,
            //     [ 'xpec_order' => 'pr_opt_val_ord.sort_order' ]
            // )->joinLeft(
            //     ['xpec_index_price' =>  $this->_productCollection->getTable('xpec_indexer_product') ],
            //     $joinIndex,
            //     [ 'xpec_order_price' => 'xpec_index_price.min_price' ]
            // );

            //////////////////////////////////////////////////////////////////////////////////////////////
            // $joinConditions=array();
            // $joinConditions2=array();
            // $joinConditions[] = 'at_discount_percent_default.row_id = e.row_id';
            // $joinConditions[] = 'at_discount_percent_default.attribute_id = \''.self::iddiscont.'\'';
            // $joinConditions[] = 'at_discount_percent_default.store_id = 0';
            // $joinConditions = implode(' AND ',$joinConditions);

            // $joinConditions2[] = 'at_discount_percent.row_id = e.row_id';
            // $joinConditions2[] = 'at_discount_percent.attribute_id = \''.self::iddiscont.'\'';
            // $joinConditions2[] = 'at_discount_percent.store_id = cat_index.store_id';
            // $joinConditions2 = implode(' AND ',$joinConditions2);

            // $this->_productCollection->getSelect()->joinLeft(
            //     ['at_discount_percent_default' =>  $this->_productCollection->getTable('catalog_product_entity_varchar') ],
            //     $joinConditions,
            //     []
            // )->joinLeft(
            //     ['at_discount_percent'         =>  $this->_productCollection->getTable('catalog_product_entity_varchar') ],
            //     $joinConditions2,
            //     ['xpec_discount_order'   => 'at_discount_percent.value']
            // );
            //echo '<pre>'.$this->_productCollection->getSelect()->__toString().'</pre>';
            //die();
            //////////////////////////////////////////////////////////////////////////////////////////////

            /*
            
                LEFT JOIN komax_prod.xpec_indexer_product indx ON ( AND indx.website_id=price_index.website_id AND price_index.customer_group_id=indx.customer_group_id)
            */
            

        }
        
        return $this->_productCollection;
    }
}