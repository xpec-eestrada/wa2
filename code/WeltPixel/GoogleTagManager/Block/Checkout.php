<?php
namespace WeltPixel\GoogleTagManager\Block;

/**
 * Class \WeltPixel\GoogleTagManager\Block\Checkout
 */
class Checkout extends \WeltPixel\GoogleTagManager\Block\Core
{
    /**
     * Returns the product details for the purchase gtm event
     * @return array
     */
    public function getProducts() {
        $quote = $this->getQuote();
        $products = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productDetail = [];
            $productDetail['name'] = html_entity_decode($item->getName());
            $productDetail['id'] = $this->helper->getGtmProductId($product);
            $productDetail['price'] = number_format($item->getBasePrice(), 2, '.', '');
            if ($this->helper->isBrandEnabled()) :
                $productDetail['brand'] = $this->helper->getGtmBrand($product);
            endif;
            $productDetail['category'] = $this->helper->getGtmCategoryFromCategoryIds($product->getCategoryIds());
            $productDetail['quantity'] = $item->getQty();
            $products[] = $productDetail;
        }

        return $products;
    }
}