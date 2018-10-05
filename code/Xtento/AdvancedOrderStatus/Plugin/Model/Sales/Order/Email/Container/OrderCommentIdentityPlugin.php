<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2016-04-19T15:54:46+00:00
 * File:          Plugin/Model/Sales/Order/Email/Container/OrderCommentIdentityPlugin.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Plugin\Model\Sales\Order\Email\Container;

use Magento\Sales\Model\Order\Email\Container\OrderCommentIdentity;

class OrderCommentIdentityPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Adjust template id for advanced order status notification
     *
     * @param OrderCommentIdentity $subject
     * @param \Closure $proceed
     * @return bool
     */
    public function aroundGetGuestTemplateId(OrderCommentIdentity $subject, \Closure $proceed)
    {
        return $this->getCustomTemplateId($subject, $proceed);
    }

    /**
     * Adjust template id for advanced order status notification
     *
     * @param OrderCommentIdentity $subject
     * @param \Closure $proceed
     * @return bool
     */
    public function aroundGetTemplateId(OrderCommentIdentity $subject, \Closure $proceed)
    {
        return $this->getCustomTemplateId($subject, $proceed);
    }

    /**
     * Adjust template id for advanced order status notification
     *
     * @param OrderCommentIdentity $subject
     * @param \Closure $proceed
     *
     * @return string
     */
    protected function getCustomTemplateId(OrderCommentIdentity $subject, \Closure $proceed)
    {
        $notificationCollection = $this->registry->registry('advancedorderstatus_notifications');
        if ($notificationCollection && $notificationCollection->getItemByColumnValue('store_id', $subject->getStore()->getStoreId())) {
            $templateId = $notificationCollection->getItemByColumnValue('store_id', $subject->getStore()->getStoreId())->getTemplateId();
            if ($templateId == 0) {
                $templateId = 'advancedorderstatus_notification';
            }
            return $templateId;
        } else {
            return $proceed();
        }
    }
}
