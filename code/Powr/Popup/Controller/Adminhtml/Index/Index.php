<?php

/**
 * Created by PhpStorm.
 * User: Alex0017
 * Date: 24.09.2017
 * Time: 10:53
 */

namespace Powr\Popup\Controller\Adminhtml\Index;
class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

}
