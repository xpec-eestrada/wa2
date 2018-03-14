<?php

namespace MagicToolbox\MagicZoom\Controller\Adminhtml\Settings;

use MagicToolbox\MagicZoom\Controller\Adminhtml\Settings;

class Index extends \MagicToolbox\MagicZoom\Controller\Adminhtml\Settings
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('magiczoom/*/edit', ['active_tab' => $activeTab]);
        return $resultRedirect;
    }
}
