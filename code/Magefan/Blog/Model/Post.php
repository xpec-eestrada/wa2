<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Model;

use Magefan\Blog\Model\Url;

/**
 * Post model
 *
 * @method \Magefan\Blog\Model\ResourceModel\Post _getResource()
 * @method \Magefan\Blog\Model\ResourceModel\Post getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 * @method $this setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method $this setMetaDescription(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getContentHeading()
 * @method $this setContentHeading(string $value)
 */
class Post extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Posts's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Base media folder path
     */
    const BASE_MEDIA_PATH = 'magefan_blog';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magefan_blog_post';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'blog_post';

    /**
     * @var \Magefan\Blog\Model\Url
     */
    protected $_url;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Post\Collection
     */
    protected $_relatedPostsCollection;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magefan\Blog\Model\Url $url
     * @param \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Url $url,
        \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_url = $url;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedPostsCollection = clone($this->getCollection());
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magefan\Blog\Model\ResourceModel\Post');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Post' : 'Posts';
    }

    /**
     * Retrieve true if post is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available post statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if post identifier exist for specific store
     * return post id if post exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Retrieve post url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_POST);
    }

    /**
     * Retrieve post url
     * @return string
     */
    public function getPostUrl()
    {
        return $this->_url->getUrl($this, URL::CONTROLLER_POST);
    }

    public function getFeaturedImage()
    {
        if (!$this->hasData('featured_image')) {
            if ($file = $this->getData('featured_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('featured_image', $image);
        }

        return $this->getData('featured_image');
    }

    /**
     * Retrieve post parent categories
     * @return \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    public function getParentCategories()
    {
        if (is_null($this->_parentCategories)) {
            $this->_parentCategories = $this->_categoryCollectionFactory->create()
                ->addFieldToFilter('category_id', array('in' => $this->getCategories()))
                ->addStoreFilter($this->getStoreId())
                ->addActiveFilter();
        }

        return $this->_parentCategories;
    }

    /**
     * Retrieve post parent categories count
     * @return int
     */
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }

    /**
     * Retrieve post related posts
     * @param  int $storeId
     * @return \Magefan\Blog\Model\ResourceModel\Post\Collection
     */
    public function getRelatedPosts($storeId = null)
    {
        if (!$this->hasData('related_posts')) {
            $collection = $this->_relatedPostsCollection
                ->addFieldToFilter('post_id', array('neq' => $this->getId()))
                ->addStoreFilter( is_null($storeId) ? $this->getStoreId() : $storeId );
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('magefan_blog_post_relatedpost')],
                'main_table.post_id = rl.related_id',
                ['position']
            )->where(
                'rl.post_id = ?',
                $this->getId()
            );
            $this->setData('related_posts', $collection);
        }

        return $this->getData('related_posts');
    }

    /**
     * Retrieve post related products
     * @param  int $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedProducts($storeId = null)
    {
        if (!$this->hasData('related_products')) {
            $collection = $this->_productCollectionFactory->create();

            if (!is_null($storeId)) {
                $collection->addStoreFilter($storeId);
            } elseif ($storeIds = $this->getStoreId()) {
                $collection->addStoreFilter($storeIds[0]);
            }

            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('magefan_blog_post_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.post_id = ?',
                $this->getId()
            );

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
    }

}
