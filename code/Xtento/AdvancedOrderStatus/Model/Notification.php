<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2016-01-05T11:51:18+00:00
 * File:          Model/Notification.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Model;

class Notification extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var Resource\Notification\Collection
     */
    protected $notificationCollection;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Notification\Collection $notificationCollection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Xtento\AdvancedOrderStatus\Model\ResourceModel\Notification\Collection $notificationCollection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->notificationCollection = $notificationCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Xtento\AdvancedOrderStatus\Model\ResourceModel\Notification');
    }

    /**
     * @param $statusCode
     * @return array
     */
    public function getNotifications($statusCode)
    {
        $notifications = [];
        $notificationCollection = $this->notificationCollection->addFieldToFilter('status_code', $statusCode);
        foreach ($notificationCollection as $notification) {
            $notifications[$notification->getStoreId()] = $notification->getTemplateId();
        }
        return $notifications;
    }
}
