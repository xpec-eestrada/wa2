<?php

namespace WeltPixel\GoogleTagManager\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_gtmOptions;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $storeCategories;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     * @var \Magento\Framework\Escaper $escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Checkout\Model\Session\SuccessValidator
     */
    protected $checkoutSuccessValidator;

    /**
     * @var \WeltPixel\GoogleTagManager\Model\Storage
     */
    protected $storage;

    /**
     * \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceCategory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Checkout\Model\Session\SuccessValidator $checkoutSuccessValidator
     * @param \WeltPixel\GoogleTagManager\Model\Storage $storage
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category $resourceCategory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Session\SuccessValidator $checkoutSuccessValidator,
        \WeltPixel\GoogleTagManager\Model\Storage $storage,
        \Magento\Cookie\Helper\Cookie $cookieHelper
    )
    {
        parent::__construct($context);
        $this->_gtmOptions = $this->scopeConfig->getValue('weltpixel_googletagmanager', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->blockFactory = $blockFactory;
        $this->registry = $registry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->resourceCategory = $resourceCategory;
        $this->escaper = $escaper;
        $this->storeCategories = [];
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->checkoutSuccessValidator = $checkoutSuccessValidator;
        $this->storage = $storage;
        $this->cookieHelper = $cookieHelper;
        $this->_populateStoreCategories();
    }


    /**
     * Get all categories id, name for the current store view
     */
    private function _populateStoreCategories()
    {
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        $storeId = $this->storeManager->getStore()->getStoreId();

        $categories = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
            ->addAttributeToSelect('name');

        foreach ($categories as $categ) {
            $this->storeCategories[$categ->getData('entity_id')] = $categ->getData('name');
        }
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return !$this->cookieHelper->isUserNotAllowSaveCookie() && $this->_gtmOptions['general']['enable'];
    }

    /**
     * @return boolean
     */
    public function trackPromotions()
    {
        return $this->_gtmOptions['general']['promotion_tracking'];
    }

    /**
     * @return boolean
     */
    public function excludeTaxFromTransaction()
    {
        return $this->_gtmOptions['general']['exclude_tax_from_transaction'];
    }

    /**
     * @return boolean
     */
    public function excludeShippingFromTransaction()
    {
        return $this->_gtmOptions['general']['exclude_shipping_from_transaction'];
    }


    /**
     * @return int
     */
    public function getImpressionChunkSize()
    {
        return $this->_gtmOptions['general']['impression_chunk_size'];
    }

    /**
     * @return string
     */
    public function getGtmCodeSnippet()
    {
        return trim($this->_gtmOptions['general']['gtm_code']);
    }

    /**
     * @return string
     */
    public function getGtmCodeSnippetPosition()
    {
        return trim($this->_gtmOptions['general']['gtm_code_position']);
    }

    /**
     * @return string
     */
    public function getGtmCodeSnippetForBody()
    {
        $gtmCodeSnippet = '';
        if ($this->getGtmCodeSnippetPosition() == \WeltPixel\GoogleTagManager\Model\Config\Source\JsCodePosition::POSITION_BODY) {
            $gtmCodeSnippet = $this->getGtmCodeSnippet();
        }

        return $gtmCodeSnippet;
    }

    /**
     * @return string
     */
    public function getGtmCodeSnippetForHead()
    {
        $gtmCodeSnippet = '';
        if ($this->getGtmCodeSnippetPosition() == \WeltPixel\GoogleTagManager\Model\Config\Source\JsCodePosition::POSITION_HEAD) {
            $gtmCodeSnippet = $this->getGtmCodeSnippet();
        }

        return $gtmCodeSnippet;
    }

    /**
     * @return string
     */
    public function getGtmNonJsCodeSnippet()
    {
        return trim($this->_gtmOptions['general']['gtm_nonjs_code']);
    }


    /**
     * @return string
     */
    public function getDataLayerScript()
    {
        $script = '';

        if (!($block = $this->createBlock('Core', 'datalayer.phtml'))) {
            return $script;
        }

        $this->addDefaultInformation();
        $this->addCategoryPageInformation();
        $this->addSearchResultPageInformation();
        $this->addProductPageInformation();
        $this->addCartPageInformation();
        $this->addCheckoutInformation();
        $this->addOrderInformation();

        $html = $block->toHtml();

        return $html;
    }

    /**
     * @param $blockName
     * @param $template
     * @return bool
     */
    protected function createBlock($blockName, $template)
    {
        if ($block = $this->blockFactory->createBlock('\WeltPixel\GoogleTagManager\Block\\' . $blockName)
            ->setTemplate('WeltPixel_GoogleTagManager::' . $template)
        ) {
            return $block;
        }

        return false;
    }

    /**
     * Set default gtm options based on configuration
     */
    public function addDefaultInformation()
    {
        if (!$this->isAdWordsRemarketingEnabled()) {
            return;
        }

        if ($this->isAdWordsRemarketingEnabled()) {
            $actionName = $this->_request->getFullActionName();
            $remarketingData = [];
            switch ($actionName) {
                case 'cms_index_index' :
                    $remarketingData['ecomm_pagetype'] = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_HOME;
                    break;
                case 'checkout_index_index' :
                    $remarketingData['ecomm_pagetype'] = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_CART;
                    break;
                default:
                    $remarketingData['ecomm_pagetype'] = \WeltPixel\GoogleTagManager\Model\Api\Remarketing::ECOMM_PAGETYPE_OTHER;
                    break;
            }
            $this->storage->setData('google_tag_params', $remarketingData);
        }
    }

    /**
     * Set category page product impressions
     */
    public function addCategoryPageInformation()
    {
        $currentCategory = $this->getCurrentCategory();

        if (!empty($currentCategory)) {
            $categoryBlock = $this->createBlock('Category', 'category.phtml');

            if ($categoryBlock) {
                $categoryBlock->setCurrentCategory($currentCategory);
                $categoryBlock->toHtml();
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Set search result page product impressions
     */
    public function addSearchResultPageInformation()
    {
        $moduleName = $this->_request->getModuleName();
        $controllerName = $this->_request->getControllerName();
        $listPrefix = '';
        if ($controllerName == 'advanced') {
            $listPrefix = __('Advanced');
        }

        if ($moduleName == 'catalogsearch') {
            $searchBlock = $this->createBlock('Search', 'search.phtml');
            if ($searchBlock) {
                $searchBlock->setListPrefix($listPrefix);
                return $searchBlock->toHtml();
            }
        }
    }

    /**
     * Set product page detail infromation
     */
    public function addProductPageInformation()
    {
        $currentProduct = $this->getCurrentProduct();

        if (!empty($currentProduct)) {
            $productBlock = $this->createBlock('Product', 'product.phtml');

            if ($productBlock) {
                $productBlock->setCurrentProduct($currentProduct);
                $productBlock->toHtml();
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Set crossell productImpressions
     */
    public function addCartPageInformation()
    {
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();

        if ($requestPath == 'checkout/cart/index') {

            $cartBlock = $this->createBlock('Cart', 'cart.phtml');

            if ($cartBlock) {
                $quote = $this->checkoutSession->getQuote();
                $cartBlock->setQuote($quote);
                $cartBlock->toHtml();
            }
        }


    }

    public function addCheckoutInformation()
    {
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();

        if ($requestPath == 'checkout/index/index') {
            $checkoutBlock = $this->createBlock('Checkout', 'checkout.phtml');

            if ($checkoutBlock) {
                $quote = $this->checkoutSession->getQuote();
                $checkoutBlock->setQuote($quote);
                $checkoutBlock->toHtml();
            }
        }
    }

    /**
     * Set purchase details
     */
    public function addOrderInformation()
    {
        $lastOrderId = $this->checkoutSession->getLastOrderId();
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();

        if ($requestPath != 'checkout/onepage/success' || !$lastOrderId) {
            return;
        }

        $orderBlock = $this->createBlock('Order', 'order.phtml');
        if ($orderBlock) {
            $order = $this->orderRepository->get($lastOrderId);
            $orderBlock->setOrder($order);
            $orderBlock->toHtml();
        }
    }

    /**
     * @return string
     */
    public function getAffiliationName()
    {
        return $this->storeManager->getWebsite()->getName() . ' - ' .
        $this->storeManager->getGroup()->getName() . ' - ' .
        $this->storeManager->getStore()->getName();
    }

    /**
     * @param int $qty
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function addToCartPushData($qty, $product)
    {
        $result = [];

        $result['event'] = 'addToCart';
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['add'] = [];
        $result['ecommerce']['add']['products'] = [];

        $productData = [];
        $productData['name'] = html_entity_decode($product->getName());
        $productData['id'] = $this->getGtmProductId($product);
        $productData['price'] = number_format($product->getFinalPrice(), 2, '.', '');
        if ($this->isBrandEnabled()) {
            $productData['brand'] = $this->getGtmBrand($product);
        }

        $productData['category'] = $this->getGtmCategoryFromCategoryIds($product->getCategoryIds());
        $productData['quantity'] = $qty;

        $result['ecommerce']['add']['products'][] = $productData;

        return $result;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Returns the product id or sku based on the backend settings
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGtmProductId($product)
    {
        $idOption = $this->_gtmOptions['general']['id_selection'];
        $gtmProductId = '';

        switch ($idOption) {
            case 'sku' :
                $gtmProductId = $product->getSku();
                break;
            case 'id' :
            default:
                $gtmProductId = $product->getId();
                break;
        }

        return $gtmProductId;
    }

    /**
     * Get the product id or sku for order item
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function getGtmOrderItemId($item)
    {
        $idOption = $this->_gtmOptions['general']['id_selection'];
        $gtmProductId = '';

        switch ($idOption) {
            case 'sku' :
                $gtmProductId = $item->getSku();
                break;
            case 'id' :
            default:
                $gtmProductId = $item->getProductId();
                break;
        }

        return $gtmProductId;
    }

    /**
     * @return boolean
     */
    public function isBrandEnabled()
    {
        return $this->_gtmOptions['general']['enable_brand'];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGtmBrand($product)
    {
        $gtmBrand = '';
        if ($this->isBrandEnabled()) {
            $brandAttribute = $this->_gtmOptions['general']['brand_attribute'];
            try {
                $frontendValue = $product->getAttributeText($brandAttribute);
                if (is_array($frontendValue)) {
                    $gtmBrand = implode(',', $product->getAttributeText($brandAttribute));
                } else {
                    $gtmBrand = trim($product->getAttributeText($brandAttribute));
                }
            } catch (\Exception $e) {
            }
        }

        return $gtmBrand;
    }

    /**
     * @return string
     */
    public function getOrderTotalCalculation()
    {
        return $this->_gtmOptions['general']['order_total_calculation'];
    }

    /**
     * Returns category tree path
     * @param array $categoryIds
     * @return string
     */
    public function getGtmCategoryFromCategoryIds($categoryIds)
    {
        if (!count($categoryIds)) {
            return '';
        }
        $categoryId = $categoryIds[0];
        $categoryPath = $this->resourceCategory->getCategoryPathById($categoryId);

        return $this->_buildCategoryPath($categoryPath);
    }

    /**
     * @param string $categoryPath
     * @return string
     */
    private function _buildCategoryPath($categoryPath)
    {
        /* first 2 categories can be ignored */
        $categoriIds = array_slice(explode('/', $categoryPath), 2);
        $categoriesWithNames = array();

        foreach ($categoriIds as $categoriId) {
            if (isset($this->storeCategories[$categoriId])) {
                $categoriesWithNames[] = $this->storeCategories[$categoriId];
            }
        }

        return implode('/', $categoriesWithNames);
    }

    /**
     * @param int $qty
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function removeFromCartPushData($qty, $product)
    {
        $result = [];

        $result['event'] = 'removeFromCart';
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['remove'] = [];
        $result['ecommerce']['remove']['products'] = [];

        $productData = [];
        $productData['name'] = html_entity_decode($product->getName());
        $productData['id'] = $this->getGtmProductId($product);
        $productData['price'] = number_format($product->getFinalPrice(), 2, '.', '');
        if ($this->isBrandEnabled()) {
            $productData['brand'] = $this->getGtmBrand($product);
        }

        $productData['category'] = $this->getGtmCategoryFromCategoryIds($product->getCategoryIds());
        $productData['quantity'] = $qty;

        $result['ecommerce']['remove']['products'][] = $productData;

        return $result;
    }

    /**
     * @param int $step
     * @param string $checkoutOption
     * @return array
     */
    public function addCheckoutStepPushData($step, $checkoutOption)
    {
        $result = [];

        $result['event'] = 'checkoutOption';
        $result['ecommerce'] = [];
        $result['ecommerce']['currencyCode'] = $this->getCurrencyCode();
        $result['ecommerce']['checkout_option'] = [];

        $optionData = [];
        $optionData['step'] = $step;
        $optionData['option'] = $checkoutOption;

        $result['ecommerce']['checkout_option']['actionField'] = $optionData;

        return $result;
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionCustomerIdEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_customerid'];
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionCustomerGroupEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_customergroup'];
    }

    /**
     * @return boolean
     */
    public function isCustomDimensionStockStatusEnabled()
    {
        return $this->_gtmOptions['general']['custom_dimension_stockstatus'];
    }

    /**
     * @return boolean
     */
    public function isAdWordConversionTrackingEnabled()
    {
        return $this->_gtmOptions['adwords_conversion_tracking']['enable'];
    }

    /**
     * @return boolean
     */
    public function isAdWordsRemarketingEnabled()
    {
        return $this->_gtmOptions['adwords_remarketing']['enable'];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function addProductClick($product, $index = 0, $list = '')
    {
        $productClickBlock = $this->createBlock('Core', 'product_click.phtml');
        $html = '';

        if ($productClickBlock) {
            $productClickBlock->setProduct($product);
            $productClickBlock->setIndex($index);

            /**
             * If a list value is set use that one, if nothing add one
             */
            if (!$list) {
                $currentCategory = $this->getCurrentCategory();
                if (!empty($currentCategory)) {
                    $list = $this->getGtmCategory($currentCategory);
                } else {
                    /* Check if it is from a listing from search or advanced search*/
                    $requestPath = $this->_request->getModuleName() .
                        DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
                        DIRECTORY_SEPARATOR . $this->_request->getActionName();
                    switch ($requestPath) {
                        case 'catalogsearch/advanced/result':
                            $list = __('Advanced Search Listing');
                            break;
                        case 'catalogsearch/result/index':
                            $list = __('Search Listing');
                            break;
                    }
                }
            }
            $productClickBlock->setList($list);
            $html = trim($productClickBlock->toHtml());
        }

        if (!empty($html)) {
            $eventCallBack = ", 'eventCallback': function() { document.location = '" .
                $this->escaper->escapeHtml($product->getUrlModel()->getUrl($product)) . "' }});";
            $html = substr(rtrim($html, ");"), 0, -1);
            $html .= $eventCallBack;
            $html = 'onclick="' . $html . '"';
        }

        return $html;
    }

    /**
     * Returns category tree path
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getGtmCategory($category)
    {
        $categoryPath = $category->getData('path');

        return $this->_buildCategoryPath($categoryPath);
    }

    /**
     * @return string
     */
    public function getDimensionsActionUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'weltpixel_gtm/index/dimensions';
    }
}
