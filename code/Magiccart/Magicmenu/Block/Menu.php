<?php
/**
 * Magiccart 
 * @category  Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license   http://www.magiccart.net/license-agreement.html
 * @Author: Magiccart<team.magiccart@gmail.com>
 * @@Create Date: 2016-02-28 10:10:00
 * @@Modify Date: 2016-06-08 14:35:59
 * @@Function:
 */
namespace Magiccart\Magicmenu\Block;

class Menu extends \Magento\Catalog\Block\Navigation
{

    /**
     * @var Category
     */
    protected $_categoryInstance;

    /**
     * Current category key
     *
     * @var string
     */
    protected $_currentCategoryKey;

    /**
     * Array of level position counters
     *
     * @var array
     */
    protected $_itemLevelPositions = [];

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_catalogCategory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $flatState;


    // +++++++++add new +++++++++

    public $_sysCfg;

    /**
     * magicmenu collection factory.
     *
     * @var \Magiccart\Magicmenu\Model\ResourceModel\Magicmenu\CollectionFactory
     */
    protected $_magicmenuCollectionFactory;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,

        // +++++++++add new +++++++++
        // \Magiccart\Magicmenu\Model\CategoryFactory $categoryFactory,
        \Magiccart\Magicmenu\Model\ResourceModel\Magicmenu\CollectionFactory $magicmenuCollectionFactory,

        array $data = []
    ) {

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogLayer = $layerResolver->get();
        $this->httpContext = $httpContext;
        $this->_catalogCategory = $catalogCategory;
        $this->_registry = $registry;
        $this->flatState = $flatState;
        $this->_categoryInstance = $categoryFactory->create();

        // +++++++++add new +++++++++
        $this->_magicmenuCollectionFactory = $magicmenuCollectionFactory;
        $this->_sysCfg= (object) $context->getScopeConfig()->getValue(
            'magicmenu',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        parent::__construct($context, $categoryFactory, $productCollectionFactory, $layerResolver, $httpContext, $catalogCategory, $registry, $flatState, $data);

    }

    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }

    public function isCategoryActive($catId)
    {
        return $this->getCurrentCategory() ? in_array($catId, $this->getCurrentCategory()->getPathIds()) : false;
    }

    public function getLogo()
    {
        $src = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $logo = '<li class="level0 logo display"><a class="level-top" href="'.$this->getHomeUrl().'"><img alt="logo" src="' .$this->getSkinUrl($src). '"></a></li>';
        return $logo;
    }

    public function getRootName()
    {
        $rootCatId = $this->_storeManager->getStore()->getRootCategoryId();
        return $this->_categoryInstance->load($rootCatId)->getName();
    }

    public function drawHomeMenu()
    {
        if($this->hasData('homeMenu')) return $this->getData('homeMenu');
        $drawHomeMenu = '';
        $active = ($this->getIsHomePage()) ? ' active' : '';
        $drawHomeMenu .= '<li class="level0 home' . $active . '">';
        $drawHomeMenu .= '<a class="level-top" href="'.$this->getBaseUrl().'"><span class="icon-home fa fa-home"></span><span class="icon-text">' .__('Home') .'</span>';
        $drawHomeMenu .= '</a>';
        if($this->_sysCfg->topmenu['demo']){
            $demo = '';
            $currentStore = $this->_storeManager->getStore();
            foreach ($this->_storeManager->getWebsites() as $website) {
                $groups = $website->getGroups();
                if(count($groups) > 1){
                    foreach ($groups as $group) {
                        $store = $group->getDefaultStore();
                        if ($store && !$store->getIsActive()) {
                            $stores = $group->getStores();
                            foreach ($stores as $store) {
                                if ($store->getIsActive()) break;
                                else $store = '';
                            }
                        }                     
                        if($store){
                            if( $store->getCode() == $currentStore->getCode() )  $demo .= '<div><a href="' .$store->getBaseUrl(). '"><span class="demo-home">'. $group->getName(). '</span></a></div>';
                            else $demo .= '<div><a href="'.$store->getBaseUrl(). 'stores/store/switch/?___store=' .$store->getCode(). '"><span class="demo-home">'. $group->getName(). '</span></a></div>';
                        }
                    }
                }
            }
            if($demo) $drawHomeMenu .= '<div class="level-top-mega">' .$demo .'</div>';           
        }

        $drawHomeMenu .= '</li>';
        $this->setData('homeMenu', $drawHomeMenu);
        return $drawHomeMenu;
    }

    public function drawMainMenu()
    {
        if($this->hasData('mainMenu')) return $this->getData('mainMenu');
        // Mage::log('your debug', null, 'yourlog.log');
        $desktopHtml = array();
		$mobileHtml  = array();
		$rootCatId = $this->_storeManager->getStore()->getRootCategoryId();
        $catListTop = $this->getChildExt($rootCatId);
        $contentCatTop  = $this->getContentCatTop();
        $extData    = array();
        foreach ($contentCatTop as $ext) {
            $extData[$ext->getCatId()] = $ext->getData();
        }
        $i = 1; $last = count($catListTop);
        $dropdownIds = explode(',', $this->_sysCfg->general['dropdown']);
        foreach ($catListTop as $catTop) :
			$idTop    = $catTop->getEntityId();
            $hasChild = $catTop->hasChildren() ? ' hasChild parent' : '';
            $isDropdown = in_array($idTop, $dropdownIds) ? ' dropdown' : '';
            $active   = $this->isCategoryActive($idTop) ? ' active' : '';
            $urlTop      =  '<a class="level-top" href="' .$catTop->getUrl(). '">' .$this->getThumbnail($catTop). '<span>' .__($catTop->getName()) . $this->getCatLabel($catTop). '</span><span class="boder-menu"></span></a>';
            $classTop    = ($i == 1) ? 'first' : ($i == $last ? 'last' : '');
            $classTop   .= $active . $hasChild .$isDropdown;

            // drawMainMenu
            if($isDropdown){ // Draw Dropdown Menu
				$childHtml = $this->getTreeCategoriesExt($idTop); // include magic_label
                $desktopHtml[$idTop] = '<li class="level0 nav-' .$i. ' cat ' . $classTop . '">' . $urlTop . $childHtml . '</li>';
                $mobileHtml[$idTop]  = '<li class="level0 nav-' .$i. ' '.$classTop.'">' . $urlTop . $childHtml . '</li>';
                $i++;
                continue;
            }
			// Draw Mega Menu 
            $data =''; $options='';
            if(isset($extData[$idTop])) $data   = $extData[$idTop];
            $blocks = array('top'=>'', 'left'=>'', 'right'=>'', 'bottom'=>'');
            if($data){
                foreach ($blocks as $key => $value) {
                    $proportion = $key .'_proportion';
                    $weight = (isset($data[$proportion])) ? $data[$proportion]:'';
                    $html = $this->getStaticBlock($data[$key]);
                    if($html) $blocks[$key] = "<div class='mage-column mega-block-$key'>".$html.'</div>';
                }
                $remove = array('top'=>'', 'left'=>'', 'right'=>'', 'bottom'=>'', 'cat_id'=>'');
                foreach ($remove as $key => $value) {
                    unset($data[$key]);
                }
                $opt     = json_encode($data);
                $options = $opt ? " data-options='$opt'" : '';
            }

			$desktopTmp = $mobileTmp  = '';
			if($hasChild || $blocks['top'] || $blocks['left'] || $blocks['right'] || $blocks['bottom']) :
				$desktopTmp .= '<div class="level-top-mega">';  /* Wrap Mega */
					$desktopTmp .='<div class="content-mega">';  /*  Content Mega */
						$desktopTmp .= $blocks['top'];
						$desktopTmp .= '<div class="content-mega-horizontal">';
							$desktopTmp .= $blocks['left'];
							if($hasChild) :
								$desktopTmp .= '<ul class="level0 mage-column cat-mega">';
								$mobileTmp .= '<ul class="submenu">';
								$childTop  = $this->getChildExt($idTop);
								foreach ($childTop as $child) {
									$id = $child->getId();
									$class = ' level1';
									$class .= $this->isCategoryActive($child->getId()) ? ' active' : '';
									$url =  '<a href="'. $child->getUrl().'"><span>'.__($child->getName()) . $this->getCatLabel($child) . '</span></a>';
									$childHtml = $this->getTreeCategoriesExt($id); // include magic_label
									// $childHtml = $this->getTreeCategoriesExtra($id); // include magic_label
									$desktopTmp .= '<li class="children' . $class . '">' . $this->getImage($child) . $url . $childHtml . '</li>';
									$mobileTmp  .= '<li class="' . $class . '">' . $url . $childHtml . '</li>';
								}
								$desktopTmp .= '<li>'  .$blocks['bottom']. '</li>';
								$desktopTmp .= '</ul>'; // end cat-mega
								$mobileTmp .= '</ul>';
							endif;
							$desktopTmp .= $blocks['right'];
						$desktopTmp .= '</div>';
						//$desktopTmp .= $blocks['bottom'];
					$desktopTmp .= '</div>';  /* End Content mega */
				$desktopTmp .= '</div>';  /* Warp Mega */
			endif;
            $desktopHtml[$idTop] = '<li class="level0 nav-' .$i. ' cat ' . $classTop . '"' . $options .'>' .$urlTop . $desktopTmp . '</li>';
            $mobileHtml[$idTop]  = '<li class="level0 nav-' .$i. ' '. $classTop . '">' . $urlTop . $mobileTmp . '</li>';
            $i++;
        endforeach;
        $menu['desktop'] = $desktopHtml;
        $menu['mobile'] = implode("\n", $mobileHtml);
        $this->setData('mainMenu', $menu);
        return $menu;
    }

    public function drawExtraMenu()
    {
        if($this->hasData('extraMenu')) return $this->getData('extraMenu');
        $extMenu    = $this->getExtraMenu();
        $count = count($extMenu);
        $drawExtraMenu = '';
        if($count){
            $i = 1; $class = 'first';
            $currentUrl = $this->getCurrentUrl();
            foreach ($extMenu as $ext) { 
                $link = $ext->getLink();
                $url = (filter_var($link, FILTER_VALIDATE_URL)) ? $link : $this->getUrl($link);
                $active = ( $link && $url == $currentUrl) ? ' active' : '';
                $html = $this->getStaticBlock($ext->getExtContent());
                if($html) $active .=' hasChild parent';
                $drawExtraMenu .= "<li class='level0 dropdown ext $active $class'>";
                    if($link) $drawExtraMenu .= '<a class="level-top" href="' .$url. '"><span>' .__($ext->getName()) . $this->getCatLabel($ext). '</span></a>';
                    else $drawExtraMenu .= '<span class="level-top"><span>' .__($ext->getName()) . $this->getCatLabel($ext). '</span></span>';
                    if($html) $drawExtraMenu .= $html; //$drawExtraMenu .= '<div class="level-top-mega">'.$html.'</div>';
                $drawExtraMenu .= '</li>';
                $i++;
                $class = ($i == $count) ? 'last' : '';  
            }
        }
        $this->setData('extraMenu', $drawExtraMenu);
        return $drawExtraMenu;
    }

    public function getChildExt($parentId)
    {
        $collection = $this->_categoryInstance->getCollection()
                        ->addAttributeToSelect(array('entity_id','name','magic_label','url_path','magic_image','magic_thumbnail'))
                        ->addAttributeToFilter('parent_id', $parentId)
                        ->addAttributeToFilter('include_in_menu', 1)
                        ->addIsActiveFilter()
                        ->addAttributeToSort('position', 'asc'); //->addOrderField('name');
        return $collection;
    }

    public function getExtraMenu()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $collection = $this->_magicmenuCollectionFactory->create()
                        ->addFieldToSelect(array('link','name','magic_label','ext_content','order'))
                        ->addFieldToFilter('extra', 1)
                        ->addFieldToFilter('status', 1);
        $collection->getSelect()->where('find_in_set(0, stores) OR find_in_set(?, stores)', $store)->order('order');
        return $collection;        
    }

    public function getStaticBlock($id)
    {
        return $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($id)->toHtml();
    }

    public function getContentCatTop()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $collection = $this->_magicmenuCollectionFactory->create()
                        ->addFieldToSelect(array(
                                'cat_id','cat_col','cat_proportion','top',
                                'right','right_proportion','bottom','left','left_proportion'
                            ))
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToFilter('status', 1);
        return $collection;
    }

    public function  getTreeCategoriesExt($parentId) // include Magic_Label
    { 
        $categories = $this->_categoryInstance->getCollection()
                        ->addAttributeToSelect(array('name','magic_label','url_path'))
                        ->addAttributeToFilter('include_in_menu', 1)
                        ->addAttributeToFilter('parent_id', $parentId)
						->addIsActiveFilter()
                        ->addAttributeToSort('position', 'asc'); 
        $html = '';
        foreach($categories as $category)
        {
            $level = $category->getLevel();
            $childHtml = $this->getTreeCategoriesExt($category->getId());
            $childClass = $childHtml ? ' hasChild parent' : '';
            $childClass .= $this->isCategoryActive($category->getId()) ? ' active' : '';
            $html .= '<li class="level' .($level -2) .$childClass. '"><a href="' . $category->getUrl(). '"><span>' .$category->getName() . $this->getCatLabel($category) . "</span></a>\n" . $childHtml . '</li>';
        }
        if($html) $html = '<ul class="level'.($level -3).' submenu">' .$html. '</ul>';
        return $html;
    }

    public function  getTreeCategoriesExtra($parentId) // include Magic_Label
    {
        $html = '';
        $categories = $this->_categoryInstance->getCategories($parentId);
        foreach($categories as $category) {
            $cat = $this->_categoryInstance->load($category->getId());
            $count = $cat->getProductCount();
            $level = $cat->getLevel();
            $childClass = $category->hasChildren() ? ' hasChild parent' : '';
            $childClass .= $this->isCategoryActive($category->getId()) ? ' active' : '';
            $html .= '<li class="level' .($level -2) .$childClass. '"><span class="alo-expand"><a href="' . $cat->getUrl(). '"><span>' .$cat->getName() . "(".$count.")" . $this->getCatLabel($cat). "</span></a>\n";
            if($childClass) $html .=  $this->getTreeCategories($category->getId());
            $html .= '</li>';
        }
        $html = '<ul class="level' .($level -3). ' submenu">' . $html . '</ul>';
        return  $html;
    }

    public function getCatLabel($cat)
    {
        $html = '';
        $label = explode(',', $cat->getMagicLabel());
        foreach ($label as $lab) {
          if($lab) $html .= '<span class="cat_label '.$lab.'">'.__(trim($lab)) .'</span>';
        }
        return $html;
    }

    public function getImage($object)
    {
        $url = false;
        $image = $object->getMagicImage();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'catalog/category/' . $image;
        }
        if($url) return '<a class="a-image" href="' .$object->getUrl(). '"><img class="img-responsive" alt="' .$object->getName(). '" src="'.$url.'"></a>';
    }

    public function getThumbnail($object)
    {
        $url = false;
        $image = $object->getMagicThumbnail();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . 'catalog/category/' . $image;
        }
        if($url) return '<img class="img-responsive" alt="' .$object->getName(). '" src="'.$url.'">';
    }

}
