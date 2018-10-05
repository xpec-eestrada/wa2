<?php

/**
 * Product:       Xtento_AdvancedOrderStatus (2.1.4)
 * ID:            %!uniqueid!%
 * Packaged:      %!packaged!%
 * Last Modified: 2017-09-01T16:39:43+00:00
 * File:          Ui/Component/Listing/Column/StatusColor.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\AdvancedOrderStatus\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\Order\Status;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Sales\Api\OrderRepositoryInterface;
use Xtento\AdvancedOrderStatus\Helper\Module;

/**
 * Class StatusColor
 * @package Xtento\AdvancedOrderStatus\Ui\Component\Listing\Column
 */
class StatusColor extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Status
     */
    protected $orderStatus;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * StatusColor constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param Status $orderStatus
     * @param Module $moduleHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        Status $orderStatus,
        Module $moduleHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->orderRepository = $orderRepository;
        $this->orderStatus = $orderStatus;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!$this->moduleHelper->isModuleEnabled()) {
            return $dataSource;
        }

        if (isset($dataSource['data']['items'])) {
            $statusColors = [];
            $orderStatuses = $this->orderStatus->getCollection()->addFieldToSelect(['status', 'color']);
            foreach ($orderStatuses as $orderStatus) {
                $statusColors[$orderStatus->getStatus()] = $orderStatus->getColor();
            }
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['status'])) {
                    $orderStatus = $item['status'];
                } else {
                    $order = $this->orderRepository->get($item['entity_id']);
                    $orderStatus = $order->getStatus();
                }
                $item[$this->getData('name')] = isset($statusColors[$orderStatus]) ? $statusColors[$orderStatus] : '';
            }
        }
        return $dataSource;
    }
}
