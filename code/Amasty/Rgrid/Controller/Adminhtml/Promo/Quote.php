<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rgrid
 */

namespace Amasty\Rgrid\Controller\Adminhtml\Promo;

abstract class Quote extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->_collectionFactory = $collectionFactory;
    }
}
