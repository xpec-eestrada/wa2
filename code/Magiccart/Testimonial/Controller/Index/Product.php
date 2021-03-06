<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-03-22 23:33:28
 * @@Function:
 */

namespace Magiccart\Testimonial\Controller\Index;

class Product extends \Magiccart\Magicproduct\Controller\Index
{
    /**
     * Default customer account page.
     */
    public function execute()
    {
    	// if ($this->getRequest()->getQuery('ajax')) {
	        $this->_view->loadLayout();
	        $this->_view->renderLayout();
	        $info = $this->getRequest()->getParam('info');
	        $type = $this->getRequest()->getParam('type');
	        $tmp = $info['timer'] ? 'product/gridtimer.phtml':'product/grid.phtml';
	        $products = $this->_view->getLayout()->createBlock('Magiccart\Magicproduct\Block\Product\GridProduct')
					            ->setCfg($info)
					           	->setActive($type)
					           	->setTemplate($tmp)
					           	->toHtml();
	        $this->getResponse()->setBody( $products );
	    // }
    }
}
