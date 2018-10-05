<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2017-09-01T16:46:08+00:00
 * File:          Observer/SalesOrderStatusSaveAfterObserver.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Observer;

class SalesOrderStatusSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Xtento\AdvancedOrderStatus\Helper\Module
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var Resource\Notification
     */
    protected $notificationResource;

    /**
     * @var NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \Magento\Sales\Model\Order\Status
     */
    private $orderStatus;

    /**
     * SalesOrderStatusSaveAfterObserver constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Xtento\AdvancedOrderStatus\Helper\Module $moduleHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Xtento\AdvancedOrderStatus\Model\ResourceModel\Notification $notificationResource
     * @param \Xtento\AdvancedOrderStatus\Model\NotificationFactory $notificationFactory
     * @param \Magento\Sales\Model\Order\Status $orderStatus
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Xtento\AdvancedOrderStatus\Helper\Module $moduleHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Xtento\AdvancedOrderStatus\Model\ResourceModel\Notification $notificationResource,
        \Xtento\AdvancedOrderStatus\Model\NotificationFactory $notificationFactory,
        \Magento\Sales\Model\Order\Status $orderStatus
    ) {
        $this->registry = $registry;
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
        $this->notificationResource = $notificationResource;
        $this->notificationFactory = $notificationFactory;
        $this->orderStatus = $orderStatus;
    }

    /**
     * After order status configuration has been saved, update the DB
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return;
        }
        $statusCode = $this->request->getParam('status', false);
        $storeNotifications = $this->request->getPost('store_notifications', false);
        if (!empty($storeNotifications) && !empty($statusCode)) {
            $this->notificationResource->removeNotifications($statusCode);
            foreach ($storeNotifications as $storeId => $templateId) {
                $notificationData = [
                    'store_id' => $storeId,
                    'status_code' => $statusCode,
                    'template_id' => $templateId
                ];
                $this->notificationFactory->create()->addData($notificationData)->save();
            }
        } else {
            if (empty($storeNotifications) && !empty($statusCode)) {
                $this->notificationResource->removeNotifications($statusCode);
            }
        }
        // Update color
        $orderStatusColor = $this->request->getPost('color_picker');
        if ($orderStatusColor !== '') {
            $orderStatus = $this->orderStatus->load($statusCode);
            if ($orderStatus->getId()) {
                if ($orderStatusColor == '#000000') {
                    $orderStatusColor = '';
                }
                $orderStatus->setColor($orderStatusColor)->save();
            }
        }
    }
}
